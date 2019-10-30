<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * DHL OnlineRetoure shipping address return item form
 *
 * @package Dhl_OnlineRetoure
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Customer_Return_Shipment extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $order;

    /**
     * @var Mage_Catalog_Helper_Image
     */
    protected $imageHelper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->imageHelper = $this->helper('catalog/image');
    }

    /**
     * Get active Order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->order === null) {
            $this->order = Mage::registry('current_order');
        }

        return $this->order;
    }

    /**
     * Get all Shipments for Order.
     *
     * @return Mage_Sales_Model_Entity_Order_Shipment_Collection
     */
    public function getShipments()
    {
        /** @var  Mage_Sales_Model_Entity_Order_Shipment_Collection $shipmentCollection */
        $shipmentCollection = $this->getOrder()->getShipmentsCollection();
        return $shipmentCollection;
    }

    /**
     * Get product thumbnail source.
     *
     * @param Mage_Sales_Model_Order_Shipment_Item $shipmentItem
     * @return string
     */
    private function getProductThumbnail(Mage_Sales_Model_Order_Shipment_Item $shipmentItem)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($shipmentItem->getProductId());
        $this->imageHelper->init($product, 'thumbnail')->resize(80, 80);
        return $this->imageHelper->__toString();
    }

    /**
     * Get product custom options if any, otherwise return empty array.
     *
     * @param Mage_Sales_Model_Order_Shipment_Item $shipmentItem
     * @return string[]
     */
    private function getProductAttributes(Mage_Sales_Model_Order_Shipment_Item $shipmentItem)
    {
        $productOptions = $shipmentItem->getOrderItem()->getProductOptions();
        return isset($productOptions['attributes_info']) ? $productOptions['attributes_info'] : array();
    }

    /**
     * Build the shipment items array.
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return Dhl_OnlineRetoure_Model_Return_Item[]
     */
    public function getShipmentItems(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $items = array();
        /** @var Mage_Sales_Model_Order_Shipment_Item $shipmentItem */
        foreach ($shipment->getAllItems() as $shipmentItem) {
            $thumbnail = $this->getProductThumbnail($shipmentItem);

            /** @var Dhl_OnlineRetoure_Model_Return_Item $item */
            $item = Mage::getModel('dhlonlineretoure/return_item');
            $item->setItemName($shipmentItem->getName());
            $item->setShipmentIncrementId($shipment->getIncrementId());
            $item->setShipmentItemEntityId($shipmentItem->getEntityId());
            $item->setSku($shipmentItem->getSku());
            $item->setQty($shipmentItem->getQty());
            $item->setThumbnail($thumbnail);
            $item->setItemOptions($this->getProductAttributes($shipmentItem));
            $items[] = $item;
        }

        return $items;
    }
}
