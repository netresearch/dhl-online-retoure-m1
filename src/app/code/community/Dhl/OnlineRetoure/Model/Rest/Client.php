<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Exception_RequestValidationException as ValidationException;

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Client
 *
 * @package Dhl_OnlineRetoure
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Client extends Varien_Http_Client
{
    /**
     * @var \Dhl_OnlineRetoure_Model_Config
     */
    protected $_config;

    /**
     * @var \Dhl_OnlineRetoure_Helper_Data
     */
    protected $_dataHelper;

    /**
     * @var int Current store
     */
    protected $_storeId;

    /**
     * Dhl_OnlineRetoure_Model_Rest_Client constructor.
     */
    public function __construct()
    {
        $this->_config = Mage::getModel('dhlonlineretoure/config');
        $this->_dataHelper = Mage::helper('dhlonlineretoure/data');

        try {
            $this->_storeId = Mage::app()->getStore()->getId();
        } catch (Mage_Core_Model_Store_Exception $exception) {
            $this->_storeId = 0;
        }

        parent::__construct();
    }

    /**
     * Perform web service request, return response.
     *
     * @param Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder $request
     * @return string[]
     * @throws Dhl_OnlineRetoure_Exception_RequestValidationException
     * @throws Zend_Http_Client_Exception
     */
    public function getReturnLabel(Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder $request)
    {
        $basicAuthUser = $this->_config->getWebserviceAuthUsername($this->_storeId);
        $basicAuthPwd = $this->_config->getWebserviceAuthPassword($this->_storeId);
        $authUser = $this->_config->getUser($this->_storeId);
        $authSignature = $this->_config->getSignature($this->_storeId);

        $authHeader = self::encodeAuthHeader($basicAuthUser, $basicAuthPwd);
        $userAuthHeader = base64_encode("{$authUser}:{$authSignature}");
        $uri = sprintf('%s/returns/', $this->_config->getEndpoint($this->_storeId));

        $this->setHeaders('Accept', 'application/json');
        $this->setHeaders('Content-Type', 'application/json');
        $this->setHeaders('Authorization', $authHeader);
        $this->setHeaders('DPDHL-User-Authentication-Token', $userAuthHeader);
        $this->setMethod('POST');
        $this->setUri($uri);
        $this->setRawData(json_encode($request));

        $response = $this->request();
        $responseBody = $response->getBody();

        if ($response->isError()) {
            if ($response->getHeader('Content-Type') === 'application/json') {
                $error = json_decode($responseBody, true);
                if (isset($error['detail'])) {
                    throw new ValidationException($error['detail']);
                }
            }

            throw new \Zend_Http_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        return json_decode($responseBody, true);
    }
}
