<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Block_Sales_Order_Email_Retoure
 *
 * @package Dhl_OnlineRetoure
 * @author  André Herrn <andre.herrn@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Sales_Order_Email_Retoure extends Mage_Core_Block_Template
{
    /**
     * Generate the return link with Hash
     *
     * @return string
     */
    public function getReturnLinkWithHash()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getData('order');

        /** @var Dhl_OnlineRetoure_Helper_Validate $helper */
        $helper = Mage::helper('dhlonlineretoure/validate');

        $hash = $helper->createHashForOrder($order);

        $msg = $helper->__("Created hash '%s' for order '%s' to send by email", $hash, $order->getIncrementId());
        $helper->log($msg, Zend_Log::INFO);

        $params = $helper->getUrlParams($order->getId(), $hash);
        return Mage::getUrl('dhlonlineretoure/create/index', $params);
    }
}
