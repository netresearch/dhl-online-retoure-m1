<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * DHL OnlineRetoure return link for customer account.
 *
 * BEWARE: This class must not extend Mage_Sales_Block_Order_Info_Buttons as
 * it is not available in CE 1.5.x. {@see getOrder()} is duplicated here instead.
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Sales_Order_Info_Buttons_Track
    extends Mage_Core_Block_Template
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Get url for online return
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getReturnTrackUrl(Mage_Sales_Model_Order $order)
    {
        $shipmentNumber = Mage::getModel('dhlonlineretoure/track')->load($order->getEntityId())->getShipmentNumber();

        if (!$shipmentNumber) {
            return '';
        }

        return Dhl_OnlineRetoure_Model_Track::TRACKING_URL . $shipmentNumber;
    }
}
