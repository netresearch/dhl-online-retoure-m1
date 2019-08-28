<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Model_Config as Config;
use Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder as ReturnOrder;
use Mage_Sales_Model_Resource_Order_Shipment_Collection as ShipmentCollection;
use Mage_Sales_Model_Order as Order;
use Mage_Sales_Model_Order_Shipment as Shipment;
use Mage_Sales_Model_Order_Shipment_Item as ShipmentItem;

/**
 * Class RequestBuilder
 *
 * @category Dhl
 * @package  Dhl_OnlineRetoure
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_RequestBuilder
{
    /**
     * @var Order
     */
    private $order ;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return ReturnOrder
     * @throws Exception
     */
    public function build()
    {
        $shippingAddress = $this->order->getShippingAddress();
        $shippingMethod = $this->order->getShippingMethod(true);

        /** @var Shipment $shipment */
        $shipment = $this->order->getShipmentsCollection()->getFirstItem();

        $countryObj = new Dhl_OnlineRetoure_Model_Rest_Request_Country();
        $countryObj->country = $shippingAddress->getCountry();
        $countryObj->countryISOCode = $shippingAddress->getCountryId();
        $countryObj->state = $shippingAddress->getRegion();

        /** @var Dhl_OnlineRetoure_Helper_Data $helper */
        $helper = Mage::helper('dhlonlineretoure/data');
        $splitStreet = $helper->splitStreet($shippingAddress->getStreetFull());

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
        if ($this->config->recipientCountryIsNonEU($shippingAddress->getCountryId())) {
            $customsDocument = new Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocument();
            $customsDocument->currency = $this->order->getOrderCurrency()->getCurrencyCode();
            $customsDocument->originalShipmentNumber = $shipment->getId();
            $customsDocument->originalOperator = $shippingMethod['carrier_code'];
            $customsDocument->positions = $this->getPositions($this->order->getShipmentsCollection());

            //todo(nr): set invoice data ?!
            $customsDocument->originalInvoiceNumber = '';
            $customsDocument->originalInvoiceDate = '';
            $customsDocument->acommpanyingDocument = '';
            $customsDocument->comment = '';
        }

        $returnOrder = new Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder();
        $returnOrder->receiverId = $this->config->getDeliveryNameByCountry($shippingAddress->getCountryId());
        $returnOrder->customerReference = $this->config->getBillingNumber($shippingAddress->getCountryId(), $this->order->getStoreId());
        $returnOrder->senderAddress = $simpleAddress;
        $returnOrder->weightInGrams = $this->getWeightInGrams($this->getShippedItems());
        $returnOrder->returnDocumentType = 'BOTH';

        $returnOrder->customsDocument = $customsDocument;
        $returnOrder->shipmentReference = $shipment->getId();
        $returnOrder->email = $shippingAddress->getEmail();
        $returnOrder->telephoneNumber = $shippingAddress->getTelephone();
        $returnOrder->value = $this->getShippedValue();

        return $returnOrder;
    }

    /**
     * @param ShipmentCollection $shipmentCollection
     * @return Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition[]
     */
    private function getPositions(ShipmentCollection $shipmentCollection)
    {
        $result = array();
        /** @var Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            /** @var ShipmentItem $item */
            foreach ($shipment->getAllItems() as $item) {
                /** @var Mage_Sales_Model_Order_Item $orderItem */
                $product = $item->getOrderItem()->getProduct();
                $position = new Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition();
                $position->positionDescription = $item->getDescription();
                $position->count = $item->getQty();
                $position->weightInGrams = $this->getWeightInGrams(array($item));
                $position->values = $item->getPrice();
                $position->originCountry = $product->getAttributeText('manufacturer');
                $position->articleReference = $product->getSku();
                $position->tarifNumber = '';

                $result[] = $position;
            }
        }

        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment_Item[] $items
     * @return float
     */
    private function getWeightInGrams(array $items)
    {
        $unitOfMeasurement = $this->config->getShipmentUnitOfMeasurement($this->order->getStoreId());
        $conversionValue = ($unitOfMeasurement === 'KG') ? 1000 : 1;
        $result = 0;
        foreach ($items as $item) {
            $result+= $item->getWeight() * $conversionValue;
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
            $result+= $item->getPrice();
        }

        return $result;
    }

    /**
     * @return ShipmentItem[]
     */
    private function getShippedItems()
    {
        /** @var ShipmentItem[] $result */
        $result = array();
        /** @var Shipment $shipment */
        foreach ($this->order->getShipmentsCollection() as $shipment) {
            $result += $shipment->getAllItems();
        }

        return $result;
    }
}
