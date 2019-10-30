<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl OnlineRetoure create return shipment controller.
 *
 * @package Dhl_OnlineRetoure
 * @author  André Herrn <andre.herrn@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_CreateController extends Dhl_OnlineRetoure_Controller_Abstract
{
    /**
     * Render the shipping address form for the customer to confirm before requesting the return shipment label.
     *
     * @return void
     */
    public function indexAction()
    {
        try {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $hash = $this->getRequest()->getParam('hash');
            $this->loadValidOrder($orderId, $hash);

            //Load and render basic layout
            $this->loadLayout();

            // set page title
            $this->getLayout()->getBlock('head')->setTitle($this->__('Check shipping address for DHL Online Return'));

            // set current navigation entry
            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('sales/order/history');
            }

            $this->renderLayout();
        } catch (Exception $e) {
            //Show error message to user
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::helper('dhlonlineretoure/data')->log($e->getMessage());

            $this->_redirect('sales/order/history');
        }
    }
}
