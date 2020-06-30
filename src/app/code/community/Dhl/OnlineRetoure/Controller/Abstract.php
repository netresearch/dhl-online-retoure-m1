<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Controller_Abstract
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Check if the user is allowed to see the page and load the current order,
     * otherwise decline access to any controller action.
     *
     * Condition for logged in users: customer can view the order.
     * Condition for guests: given hash is valid.
     *
     * @param  int|null $orderId
     * @param  string|null $hash
     * @return boolean
     * @throws Dhl_OnlineRetoure_Exception_OrderValidationException
     * @throws Mage_Core_Exception
     */
    protected function loadValidOrder($orderId = null, $hash = null)
    {
        /** @var Dhl_OnlineRetoure_Helper_Validate $validateHelper */
        $validateHelper = Mage::helper('dhlonlineretoure/validate');

        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }

        //Pre-check if order_id is given
        $validateHelper->isOrderIdValid($orderId);

        if (null === $hash) {
            $hash = $this->getRequest()->getParam('hash');
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        // Check hash case
        $validateHelper->isHashRequest()
            && $validateHelper->isHashValid($hash, $order)
            && $validateHelper->isOrderValid($order);

        // Check internal case
        $validateHelper->isInternalRequest()
            && $validateHelper->isCustomerValid($order)
            && $validateHelper->isOrderValid($order);

        Mage::unregister('current_order');
        Mage::register('current_order', $order);
        return true;
    }
}
