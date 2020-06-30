<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Exception_OrderValidationException as AuthorizationException;
use Dhl_OnlineRetoure_Exception_RequestValidationException as ValidationException;

/**
 * Dhl OnlineRetoure label controller
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_LabelController extends Dhl_OnlineRetoure_Controller_Abstract
{
    /**
     * Only accept POST requests to this controller
     *
     * @return $this
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isPost()) {
            $this->getResponse()
                 ->setHeader('HTTP/1.1', '404 Not Found')
                 ->setHeader('Status', '404 File not found');

            $this->_forward('noRoute');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        } elseif (!$this->_validateFormKey()) {
            $this->_redirect('sales/order/history');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        return $this;
    }
    /**
     * Remove empty entries from nested array.
     *
     * @param string[] $data
     * @return string[]
     */
    private function filterItems(array $data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->filterItems($value);
            }
        }

        return array_filter($data);
    }

    /**
     * Set data from confirmation form to shipping address
     *
     * @param Mage_Sales_Model_Order $order
     * @param string[] $postData
     */
    protected function _setShippingAddress(Mage_Sales_Model_Order $order, array $postData)
    {
        $order->getShippingAddress()
              ->setFirstname($postData['firstname'])
              ->setLastname($postData['lastname'])
              ->setCompany($postData['company'])
              ->setCity($postData['city'])
              ->setPostcode($postData['postcode'])
              ->setCountryId($postData['country_id'])
              ->setStreetFull($postData['street']);
    }

    /**
     * @throws Exception
     */
    public function formPostAction()
    {
        $orderId = (int) $this->getRequest()->getParam('order_id');
        $hash = $this->getRequest()->getParam('hash');
        $helper = Mage::helper('dhlonlineretoure/validate');

        try {
            $this->loadValidOrder($orderId, $hash);
        } catch (AuthorizationException $exception) {
            // user is not authorized to access this particular order
            $helper->log($exception->getMessage());
            Mage::getSingleton('core/session')->addError($exception->getMessage());

            $this->_redirect('sales/order/history');
            return;
        } catch (\Exception $exception) {
            // other error occurred
            $helper->log($exception->getMessage(), Zend_Log::ERR);
            $msg = $helper->__('An error occurred while retrieving the return label. Please contact customer support.');
            Mage::getSingleton('core/session')->addError($msg);

            $this->_redirect('sales/order/history');
            return;
        }

        // order access authorized, merge form data with shipping address
        $order = Mage::registry('current_order');
        $this->_setShippingAddress($order, $this->getRequest()->getPost());

        // prepare web service request
        $returnItems = $this->getRequest()->getParam('returns');
        $returnItems = $this->filterItems($returnItems);

        /** @var Dhl_OnlineRetoure_Model_Rest_RequestBuilder $requestBuilder */
        $requestBuilder = Mage::getSingleton('dhlonlineretoure/rest_requestBuilder');
        $requestBuilder->setOrder($order);
        $requestBuilder->setReturnInformation($returnItems);

        /** @var Dhl_OnlineRetoure_Model_Rest_Client $restClient */
        $restClient = Mage::getModel('dhlonlineretoure/rest_client');

        try {
            // build and send web service request
            $request = $requestBuilder->build();
            $response = $restClient->getReturnLabel($request);
        } catch (ValidationException $exception) {
            Mage::getSingleton('core/session')->addError($exception->getMessage());
            $params = $helper->getUrlParams($orderId, $hash);
            $this->_redirectError(Mage::getUrl('*/create/index', $params));
            return;
        } catch (\Zend_Http_Client_Exception $exception) {
            // web service communication did not succeed
            $msg = $helper->__('An error occurred while retrieving the return label. Please contact customer support.');
            Mage::getSingleton('core/session')->addError($msg);
            $params = $helper->getUrlParams($orderId, $hash);
            $this->_redirectError(Mage::getUrl('*/create/index', $params));
            return;
        }

        /** @var Dhl_OnlineRetoure_Model_Return_LabelGenerator $labelGenerator */
        $labelGenerator = Mage::getSingleton('dhlonlineretoure/return_labelGenerator');
        $labelData = $labelGenerator->combineLabelsPdf($response);
        $filename = $labelGenerator->getFilename($order, $response['shipmentNumber']);

        $this->_prepareDownloadResponse($filename, $labelData, 'application/pdf');
        // add entry to order comments
        $comment = $this->__(
            'Return label with shipment number %s successfully created on %s.',
            $response['shipmentNumber'],
            Mage::getSingleton('core/date')->date()
        );
        $this->addOrderComment($order, $comment, true);

        $trackComment = $this->addRetoureTracking($orderId, (int)$response['shipmentNumber']);
        $this->addOrderComment($order, $trackComment);
    }

    /**
     * @param int $orderId
     * @param int $shipmentId
     * @return string
     * @throws Exception
     */
    private function addRetoureTracking($orderId, $shipmentId)
    {
        $trackModel = Mage::getModel("dhlonlineretoure/track");
        $trackModel->setOrderId($orderId);
        $trackModel->setShipmentNumber($shipmentId);
        $trackModel->save();
        $url = Dhl_OnlineRetoure_Model_Track::TRACKING_URL . $shipmentId;
        $comment = $this->__('Track Return #<a href="%s">%s</a>', $url, $shipmentId);

        return $comment;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $comment
     * @param bool $isVisible
     * @throws Exception
     */
    private function addOrderComment($order, $comment, $isVisible = false)
    {
        /** @var Mage_Sales_Model_Order_Status_History $history */
        $history = $order->addStatusHistoryComment($comment);
        $history->setIsVisibleOnFront($isVisible);
        $history->save();
    }
}
