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
use Dhl_OnlineRetoure_Model_Adminhtml_System_Config_Source_Procedure as Procedure;

/**
 * DHL OnlineRetoure Config Model
 *
 * @category    Dhl
 * @package     Dhl_OnlineRetoure
 * @author      André Herrn <andre.herrn@netresearch.de>
 * @author      Christoph Aßmann <christoph.assmann@netresearch.de>
 */
class Dhl_OnlineRetoure_Model_Config
{
    const CONFIG_XML_PATH_SANDBOX_MODE ='shipping/dhlonlineretoure/sandbox_mode';
    const CONFIG_XML_PATH_SANDBOX_ENDPOINT = 'shipping/dhlonlineretoure/sandbox_endpoint';
    const CONFIG_XML_PATH_SANDBOX_AUTH_USERNAME = 'shipping/dhlonlineretoure/sandbox_auth_username';
    const CONFIG_XML_PATH_SANDBOX_AUTH_PASSWORD = 'shipping/dhlonlineretoure/sandbox_auth_password';
    const CONFIG_XML_PATH_SANDBOX_USER_NAME ='shipping/dhlonlineretoure/sandbox_account_user';
    const CONFIG_XML_PATH_SANDBOX_USER_PASSWORD = 'shipping/dhlonlineretoure/sandbox_account_signature';
    const CONFIG_XML_PATH_SANDBOX_DELIVERY_NAMES = 'shipping/dhlonlineretoure/sandbox_delivery_names';
    const CONFIG_XML_PATH_SANDBOX_ACCOUNT_PARTICIPATION = 'shipping/dhlonlineretoure/sandbox_account_participation';
    const CONFIG_XML_PATH_SANDBOX_ACCOUNT_EKP = 'shipping/dhlonlineretoure/sandbox_account_ekp';

    const CONFIG_XML_PATH_PRODUCTION_ENDPOINT = 'shipping/dhlonlineretoure/production_endpoint';
    const CONFIG_XML_PATH_PRODUCTION_AUTH_USERNAME = 'shipping/dhlonlineretoure/production_account_user';
    const CONFIG_XML_PATH_PRODUCTION_AUTH_PASSWORD = 'shipping/dhlonlineretoure/production_account_password';
    const CONFIG_XML_PATH_PRODUCTION_USER_NAME = 'shipping/dhlonlineretoure/account_user';
    const CONFIG_XML_PATH_PRODUCTION_USER_PASSWORD = 'shipping/dhlonlineretoure/account_signature';
    const CONFIG_XML_PATH_PRODUCTION_ACCOUNT_EKP = 'shipping/dhlonlineretoure/account_ekp';
    const CONFIG_XML_PATH_PRODUCTION_ACCOUNT_PARTICIPATION = 'shipping/dhlonlineretoure/account_participation';
    const CONFIG_XML_PATH_PRODUCTION_DELIVERY_NAMES = 'shipping/dhlonlineretoure/delivery_names';

    const CONFIG_XML_PATH_UNIT_OF_MEASUREMENT = 'shipping/dhlonlineretoure/shipment_unitofmeasure';


    /**
     * Check if online return is enabled.
     *
     * @param mixed $storeId
     * @return boolean
     */
    public function isEnabled($storeId = null)
    {
        return (bool)Mage::getStoreConfig('shipping/dhlonlineretoure/active', $storeId);
    }

    /**
     * Check if online return logging is enabled.
     *
     * @param mixed $storeId
     * @return boolean
     */
    public function isLoggingEnabled($storeId = null)
    {
        return (bool)Mage::getStoreConfig('shipping/dhlonlineretoure/logging_enabled', $storeId);
    }

    /**
     * Obtain CMS page url key
     *
     * @param mixed $storeId
     * @return string
     */
    public function getCmsRevocationPage($storeId = null)
    {
        $page = Mage::getStoreConfig('shipping/dhlonlineretoure/cms_revocation_page', $storeId);
        if (!$page) {
            return '';
        }

        return $page;
    }

    /**
     * Get delivery name config value by ISO 3166 ALPHA-2 country ID.
     *
     * @param string $iso2Code
     * @param mixed $storeId
     * @return string Delivery name if available for given country, empty string otherwise.
     * @throws Exception
     * @link http://www.iso.org/iso/country_codes/iso_3166_code_lists/country_names_and_code_elements.htm
     */
    public function getDeliveryNameByCountry($iso2Code, $storeId = null)
    {
        if (!is_string($iso2Code) || (strlen($iso2Code) !== 2)) {
            throw new Exception('Please provide valid two-character country code.');
        }

        if ($this->isSandboxModeEnabled($storeId)) {
            $deliverynames = unserialize(Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_DELIVERY_NAMES, $storeId));
        } else {
            $deliverynames = unserialize(Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_DELIVERY_NAMES, $storeId));
        }

        if (!is_array($deliverynames)) {
            return '';
        }

        foreach ($deliverynames as $data) {
            if (strcasecmp($data['iso'], $iso2Code) === 0) {
                return $data['name'];
            }
        }

        return '';
    }

    /**
     * Obtain all country codes that are valid return shipment origins
     */
    public function getAllowedCountryCodes()
    {
        $countryCodes = Mage::getStoreConfig('shipping/dhlonlineretoure/allowed_countries');
        return explode(',', $countryCodes);
    }

    /**
     * Get allowed shipping methods with or without DHL Versenden functionalities
     *
     * @return array
     */
    public function getAllowedShippingMethods()
    {
        $originalMethods = Mage::getStoreConfig('shipping/dhlonlineretoure/allowed_shipping_methods');
        $originalMethods = explode(",", $originalMethods);

        $dhlMethods = array_map(
            function ($shippingMethod) {
                // calculate DHL Versenden counterpart to original shipping method
                return preg_replace('/^[^_]+_(.+)$/', 'dhlversenden_$1', $shippingMethod);
            },
            $originalMethods
        );

        return array_merge($originalMethods, $dhlMethods);
    }

    /**
     * Check if shipping method is allowed
     *
     * @param  string $shippingMethod
     * @return boolean
     */
    public function isAllowedShippingMethod($shippingMethod)
    {
        $allowedShippingMethods = $this->getAllowedShippingMethods();

        return in_array($shippingMethod, $allowedShippingMethods);
    }

    /**
     * @param $store
     * @return bool
     */
    public function isSandboxModeEnabled($store=null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_SANDBOX_MODE, $store);
    }

    /**
     * Obtain username for CIG authentication.
     *
     * @return string
     */
    public function getWebserviceAuthUsername()
    {
        if ($this->isSandboxModeEnabled()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_AUTH_USERNAME);
        }

        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_AUTH_USERNAME);
    }

    /**
     * Obtain password for CIG authentication.
     *
     * @return string
     */
    public function getWebserviceAuthPassword()
    {
        if ($this->isSandboxModeEnabled()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_AUTH_PASSWORD);
        }

        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_AUTH_PASSWORD);
    }

    /**
     * Obtain the webservice endpoint address (location).
     *
     * @return string
     */
    public function getEndpoint()
    {
        if ($this->isSandboxModeEnabled()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_ENDPOINT);
        }

        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_ENDPOINT);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getUserName($store = null)
    {
        if ($this->isSandboxModeEnabled()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_USER_NAME, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_USER_NAME, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getUserPassword($store = null)
    {
        if ($this->isSandboxModeEnabled()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_USER_PASSWORD, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_USER_PASSWORD, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getEKP($store = null)
    {
        if ($this->isSandboxModeEnabled()) {
            return Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_ACCOUNT_EKP, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_ACCOUNT_EKP, $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getAccountParticipation($store = null)
    {
        if ($this->isSandboxModeEnabled()) {
            $participations = Mage::getStoreConfig(self::CONFIG_XML_PATH_SANDBOX_ACCOUNT_PARTICIPATION, $store);
        } else {
            $participations = Mage::getStoreConfig(self::CONFIG_XML_PATH_PRODUCTION_ACCOUNT_PARTICIPATION, $store);
        }

        $participation = array();
        foreach ($participations as $participationEntry) {
            $participation[$participationEntry['procedure']] = $participationEntry['participation'];
        }


        return $participation;
    }

    /**
     * @param $recipientCountry
     * @param null $store
     * @return string
     */
    public function getBillingNumber($recipientCountry, $store = null)
    {
        $ekp = $this->getEKP($store);
        $participationNumbers = $this->getAccountParticipation($store);

        $participationNumber = $participationNumbers[Procedure::PROCEDURE_RETURNSHIPMENT_NATIONAL] ;
        if ($this->recipientCountryIsNonEU($recipientCountry)) {
            $participationNumber = $participationNumbers[Procedure::PROCEDURE_RETURNSHIPMENT_INTERNATIONAL] ;
        }


        return $ekp . $participationNumber;
    }

    /**
     * @param string $recipientCountry
     * @return bool
     */
    public function recipientCountryIsNonEU($recipientCountry)
    {
        $euCountryArray= explode(',', Mage::getStoreConfig('general/country/eu_countries'));
        return (!in_array($recipientCountry, $euCountryArray, true));
    }

    /**
     * @param null $store
     * @return string
     */
    public function getShipmentUnitOfMeasurement($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_UNIT_OF_MEASUREMENT, $store);
    }
}
