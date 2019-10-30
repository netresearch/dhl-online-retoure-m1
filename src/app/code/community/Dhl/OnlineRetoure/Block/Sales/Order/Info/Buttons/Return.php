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
 * @author  André Herrn <andre.herrn@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Sales_Order_Info_Buttons_Return
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
    public function getReturnUrl(Mage_Sales_Model_Order $order)
    {
        /** @var Dhl_OnlineRetoure_Helper_Validate $helper */
        $helper = Mage::helper('dhlonlineretoure/validate');
        if (!$helper->canShowRetoureLink($order)) {
            return '';
        }

        return $this->getUrl('dhlonlineretoure/create/index', array('order_id' => $order->getId()));
    }

}
