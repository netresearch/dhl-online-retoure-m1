<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Exception_RequestValidationException as ValidationException;

/**
 * Class RequestBuilder
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_RequestBuilder
{
    /**
     * @var Mage_Sales_Model_Order
     */
    private $order;

    /**
     * @var string[][]
     */
    private $returnInformation;

    /**
     * @var Dhl_OnlineRetoure_Model_Config
     */
    private $config;

    /**
     * Initialize dependencies.
     */
    public function __construct()
    {
        $this->config = Mage::getSingleton('dhlonlineretoure/config');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param string[][] $returnInformation
     */
    public function setReturnInformation(array $returnInformation)
    {
        $this->returnInformation = $returnInformation;
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment_Item[] $items
     * @return float
     */
    private function getWeightInGrams(array $items)
    {
        $unitOfMeasurement = $this->config->getWeightUnit($this->order->getStoreId());
        $conversionValue = ($unitOfMeasurement === 'KG') ? 1000 : 1;
        $result = 0;
        foreach ($items as $item) {
            $result += $item->getWeight() * $conversionValue;
        }

        return $result;
    }

    /**
     * @return float
     */
    private function getShippedValue()
    {
        $result = 0;
        foreach ($this->getShippedItems() as $item) {
            $result += $item->getOrderItem()->getBaseRowTotalInclTax();
        }

        return $result;
    }

    /**
     * @return Mage_Sales_Model_Order_Shipment_Item[]
     */
    private function getShippedItems()
    {
        $itemIds = array();
        foreach ($this->returnInformation['shipments'] as $shipment) {
            $itemIds[] = array_keys($shipment['items']);
        }

        /** @var Mage_Sales_Model_Resource_Order_Shipment_Item_Collection $itemCollection */
        $itemCollection = Mage::getResourceModel('sales/order_shipment_item_collection');
        $itemCollection = $itemCollection->addFieldToFilter('entity_id', array('in' => $itemIds))->load();

        return $itemCollection->getItems();
    }

    /**
     * @return Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition[]
     */
    private function getPositions()
    {
        $result = array();
        foreach ($this->getShippedItems() as $item) {
            $position = new Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition();
            $position->positionDescription = $item->getName();
            $position->count = (int) $item->getQty();
            $position->weightInGrams = $this->getWeightInGrams(array($item));
            $position->values = (float) $item->getOrderItem()->getBasePriceInclTax();
            $position->articleReference = $item->getOrderItem()->getSku();
            $position->tarifNumber = '';

            $product = $item->getOrderItem()->getProduct();
            $countryOfManufacture = $product ? (string) $product->getData('country_of_manufacture') : '';
            if ($countryOfManufacture) {
                /** @var Mage_Directory_Model_Country $countryModel */
                $countryModel = Mage::getSingleton('directory/country')->loadByCode($countryOfManufacture);
                $position->originCountry = $countryModel->getIso3Code();
            }

            $result[] = $position;
        }

        return $result;
    }

    /**
     * @return Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder
     * @throws ValidationException
     */
    public function build()
    {
        if (empty($this->returnInformation['shipments'])) {
            throw new ValidationException(Mage::helper('dhlonlineretoure/data')->__('Please select items to return.'));
        }

        $shippingAddress = $this->order->getShippingAddress();
        $shippingMethod = $this->order->getShippingMethod(true);

        $countryId =  $shippingAddress->getCountryId();
        /** @var Mage_Directory_Model_Country $countryDirectory */
        $countryDirectory = Mage::getSingleton('directory/country')->loadByCode($countryId);

        $countryObj = new Dhl_OnlineRetoure_Model_Rest_Request_Country();
        $countryObj->country = $shippingAddress->getCountry();
        $countryObj->countryISOCode =  $countryDirectory->getIso3Code();
        $countryObj->state = $shippingAddress->getRegion();

        $splitStreet = Mage::helper('dhlonlineretoure/data')->splitStreet($shippingAddress->getStreetFull());

        $houseNumber = $splitStreet['street_number'];
        $streetName = $splitStreet['street_name'];

        $simpleAddress = new Dhl_OnlineRetoure_Model_Rest_Request_SimpleAddress();
        $simpleAddress->name1 = $shippingAddress->getName();
        $simpleAddress->streetName = $streetName;
        $simpleAddress->houseNumber = $houseNumber;
        $simpleAddress->postCode = $shippingAddress->getPostcode();
        $simpleAddress->city = $shippingAddress->getCity();

        /** optional simpleAddress params */
        $simpleAddress->country = $countryObj;
        $simpleAddress->name2 = $shippingAddress->getStreet2();
        $simpleAddress->name3 = $shippingAddress->getStreet3();

        $customsDocument = null;
        if (!$this->config->isEuCountry($shippingAddress->getCountryId())) {
            $shipmentIds = array_keys($this->returnInformation['shipments']);
            $customsDocument = new Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocument();
            $customsDocument->currency = $this->order->getBaseCurrency()->getCurrencyCode();
            $customsDocument->originalShipmentNumber = implode(',', $shipmentIds);
            $customsDocument->originalOperator = $shippingMethod['carrier_code'];
            $customsDocument->positions = $this->getPositions();
        }

        $returnOrder = new Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder();
        $returnOrder->receiverId = $this->config->getReceiverId(
            $shippingAddress->getCountryId(),
            $this->order->getStoreId()
        );

        $returnOrder->customerReference = $this->config->getBillingNumber(
            $shippingAddress->getCountryId(),
            $this->order->getStoreId()
        );
        $returnOrder->senderAddress = $simpleAddress;
        $returnOrder->weightInGrams = $this->getWeightInGrams($this->getShippedItems());
        $returnOrder->returnDocumentType = 'BOTH';

        $returnOrder->customsDocument = $customsDocument;
        $returnOrder->shipmentReference = $this->order->getIncrementId();
        $returnOrder->email = $shippingAddress->getEmail();
        $returnOrder->telephoneNumber = $shippingAddress->getTelephone();
        $returnOrder->value = $this->getShippedValue();

        // reset builder data
        $this->order = null;
        $this->returnInformation = array();

        return $returnOrder;
    }
}
