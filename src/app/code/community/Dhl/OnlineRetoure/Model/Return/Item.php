<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * DHL OnlineRetoure return item model.
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Return_Item extends Varien_Object
{
    /**
     * @var string
     */
    protected $shipmentIncrementId;

    /**
     * @var string
     */
    protected $shipmentItemEntityId;

    /**
     * @var string
     */
    protected $itemName;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var int
     */
    protected $qty = 0;

    /**
     * @var string[]
     */
    protected $itemOptions;

    /**
     * @var string
     */
    protected $thumbnail;

    /**
     * @return string
     */
    public function getShipmentIncrementId()
    {
        return $this->shipmentIncrementId;
    }

    /**
     * @param string $shipmentIncrementId
     */
    public function setShipmentIncrementId($shipmentIncrementId)
    {
        $this->shipmentIncrementId = $shipmentIncrementId;
    }

    /**
     * @return string
     */
    public function getShipmentItemEntityId()
    {
        return $this->shipmentItemEntityId;
    }

    /**
     * @param string $shipmentItemEntityId
     */
    public function setShipmentItemEntityId($shipmentItemEntityId)
    {
        $this->shipmentItemEntityId = $shipmentItemEntityId;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param string $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @return string[]
     */
    public function getItemOptions()
    {
        return $this->itemOptions;
    }

    /**
     * @param string[] $itemOptions
     */
    public function setItemOptions(array $itemOptions)
    {
        $this->itemOptions = $itemOptions;
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }
}
