<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Model_Track
 *
 * @link    https://www.netresearch.de/
 */

class Dhl_OnlineRetoure_Model_Track  extends Mage_Core_Model_Abstract
{
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_SHIPMENT_NUMBER = 'shipment_number';
    const TRACKING_URL = 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?lang=de&idc=';

    public function _construct()
    {
        $this->_init('dhlonlineretoure/track');
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::FIELD_ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::FIELD_ORDER_ID, $orderId);
    }

    /**
     * @return int
     */
    public function getShipmentNumber()
    {
        return $this->getData(self::FIELD_SHIPMENT_NUMBER);
    }

    /**
     * @param int $shipmentNumber
     * @return void
     */
    public function setShipmentNumber($shipmentNumber)
    {
        $this->setData(self::FIELD_SHIPMENT_NUMBER, $shipmentNumber);
    }
}
