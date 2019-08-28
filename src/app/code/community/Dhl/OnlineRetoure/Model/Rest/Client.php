<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder as ReturnOrder;
use Dhl_OnlineRetoure_Model_Config as ModuleConfig;

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Client
 *
 * @category Dhl
 * @package  Dhl_OnlineRetoure
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Client extends Varien_Http_Client
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var Dhl_OnlineRetoure_Helper_Data
     */
    protected $dataHelper;

    /**
     * Dhl_OnlineRetoure_Model_Rest_Client constructor.
     */
    public function __construct()
    {
        $this->moduleConfig = Mage::getModel('dhlonlineretoure/config');
        $this->dataHelper = Mage::helper('dhlonlineretoure/data');

        parent::__construct();
    }

    /**
     * @param ReturnOrder $request
     * @return mixed
     * @throws \Exception
     */
    public function getReturnLabel(ReturnOrder $request)
    {
        $authHeader = self::encodeAuthHeader(
            $this->moduleConfig->getWebserviceAuthUsername(),
            $this->moduleConfig->getWebserviceAuthPassword()
        );
        $userAuthHeader = base64_encode(
            $this->moduleConfig->getUserName(). ':' . $this->moduleConfig->getUserPassword()
        );

        $this->setHeaders('Accept', 'application/json');
        $this->setHeaders('Content-Type', 'application/json');
        $this->setHeaders('Authorization', $authHeader);
        $this->setHeaders('DPDHL-User-Authentication-Token', $userAuthHeader);
        $this->setMethod('POST');
        $this->setUri($this->moduleConfig->getEndpoint() .'/returns/');
        $this->setRawData(json_encode($request));

        try {
            $response = $this->request();
        } catch (Zend_Http_Client_Exception $e) {
            $message = sprintf('There was a error during return label request. Error = %s', $e->getMessage());
            $this->dataHelper->log($message, Zend_Log::ERR);

            throw new Exception($e->getMessage(), Zend_Log::ERR);
        }

        return json_decode($response->getBody(), true);
    }
}
