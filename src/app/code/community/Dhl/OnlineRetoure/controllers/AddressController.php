<?php
/**
 * Dhl OnlineRetoure
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Dhl
 * @package     Dhl_OnlineRetoure
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dhl OnlineRetoure address controller
 *
 * @category   Dhl
 * @package    Dhl_OnlineRetoure
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Christoph Aßmann <christoph.assmann@netresearch.de>
 */
class Dhl_OnlineRetoure_AddressController extends Dhl_OnlineRetoure_Controller_Abstract
{
    /**
     * Set data from confirmation form to shipping address
     *
     * @param Mage_Sales_Model_Order $order
     * @param array $postData
     */
    protected function _setShippingAddress(Mage_Sales_Model_Order $order, array $postData)
    {
        $order->getShippingAddress()
            ->setFirstname($postData['firstname'])
            ->setLastname($postData['lastname'])
            ->setCompany($postData['company'])
            ->setStreetFull($postData['street'])
            ->setCity($postData['city'])
            ->setPostcode($postData['postcode'])
            ->setCountryId($postData['country_id'])
        ;
    }

    /**
     * Send PDF to customer, add status history comment to order.
     *
     * @param stdClass $response
     * @param Mage_Sales_Model_Order $order
     * @return Dhl_OnlineRetoure_AddressController
     */
    protected function _printPdf(stdClass $response, Mage_Sales_Model_Order $order)
    {
        $localeDate = Mage::app()->getLocale()->date($response->issueDate);
        $filenameDate = Mage::getSingleton('core/date')->date('Y-m-d', $response->issueDate);

        $this->_prepareDownloadResponse(
            sprintf(
                "%s_Return_%s_%s.pdf",
                str_replace(" ", "_", Mage::app()->getStore()->getName()),
                $order->getIncrementId(),
                $filenameDate
            ),
            base64_decode($response->labelData),
            'application/pdf'
        );

        $comment = 'Return label with shipment number %s successfully created on %s.';
        $order
            ->addStatusHistoryComment($this->__($comment, $response->shipmentNumber, $localeDate))
            ->setIsVisibleOnFront(true)
            ->save();

        Mage::helper("dhlonlineretoure/validate")->logSuccess();
        return $this;
    }

    /**
     * Render the shipping address form
     * for the customer to confirm before requesting the retoure label
     *
     * @return void
     */
    public function confirmAction()
    {
        try {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $hash    = $this->getRequest()->getParam('hash');
            $this->loadValidOrder($orderId, $hash);

            //Load and render basic layout
            $this->loadLayout();

            // set page title
            $title = $this->getLayout()->getBlock('dhlonlineretoure_customer_address_edit')->getTitle();
            $this->getLayout()->getBlock('head')->setTitle($this->__($title));

            // set current navigation entry
            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('sales/order/history');
            }

            $this->renderLayout();
        } catch (Exception $e) {
            //Show error message to user
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::helper("dhlonlineretoure/data")->log($e->getMessage());

            $this->_redirect('*/*/error');
        }
    }

    /**
     * @return Dhl_OnlineRetoure_AddressController|Mage_Core_Controller_Varien_Action
     */
    public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('sales/order/history');
        }

        $orderId = (int) $this->getRequest()->getParam('order_id');
        $hash    = $this->getRequest()->getParam('hash');

        /* @var $client Dhl_OnlineRetoure_Model_Rest_Client */
        $client = Mage::getModel('dhlonlineretoure/rest_client');

        try {
            $this->loadValidOrder($orderId, $hash);

            // Send data
            if ($this->getRequest()->isPost()) {
                /* @var $order Mage_Sales_Model_Order */
                $order = Mage::registry('current_order');
                $this->_setShippingAddress($order, $this->getRequest()->getPost());

                /** @var Dhl_OnlineRetoure_Model_Rest_RequestBuilder $requestBuilder */
                $requestBuilder = Mage::getModel('dhlonlineretoure/rest_requestBuilder');

                /** @var Dhl_OnlineRetoure_Model_Config $config */
                $config = Mage::getModel('dhlonlineretoure/config');
                $requestBuilder->setOrder($order);
                $requestBuilder->setConfig($config);

                $request = $requestBuilder->build();
                $response = (object) $client->getReturnLabel($request);

                if ($client->getLastResponse()->isError()) {
                    $message = Mage::helper('dhlonlineretoure/data')->__(
                        'There was a error during return label request. Please contact customer support.'
                    );
                    throw new Exception($message, Zend_Log::ERR);
                }

                return $this->_printPdf($response, $order);
            }

        } catch (Dhl_OnlineRetoure_Model_Validate_Exception $e) {

            // error while accessing page
            Mage::helper('dhlonlineretoure/data')->log($e->getMessage());
            Mage::getSingleton('core/session')->addError($e->getMessage());

        } catch (Exception $e) {

            $this->getSession()->setAddressFormData($this->getRequest()->getPost());

            // error while performing the web service request -> user message
            $message = $this->__('The web service request failed with the following error message: "%s"', $e->getMessage());
            Mage::getSingleton('core/session')->addError($message);


            $params = Mage::helper('dhlonlineretoure/validate')->getUrlParams($orderId, $hash);
            return $this->_redirectError(Mage::getUrl('*/*/confirm', $params));

        }

        Mage::helper('dhlonlineretoure/validate')->logFailure();
        return $this->_redirectError(Mage::getUrl('*/*/error'));
    }
}
