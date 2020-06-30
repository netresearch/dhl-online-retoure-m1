<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Model_Adminhtml_System_Config_Source_Procedure as Procedure;

/**
 * DHL OnlineRetoure Config Model
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Config
{
    const CONFIG_PATH_SANDBOX_MODE ='shipping/dhlonlineretoure/sandbox_mode';
    const CONFIG_PATH_SANDBOX_ENDPOINT = 'shipping/dhlonlineretoure/sandbox_endpoint';
    const CONFIG_PATH_SANDBOX_AUTH_USERNAME = 'shipping/dhlonlineretoure/sandbox_auth_username';
    const CONFIG_PATH_SANDBOX_AUTH_PASSWORD = 'shipping/dhlonlineretoure/sandbox_auth_password';
    const CONFIG_PATH_SANDBOX_USER_NAME ='shipping/dhlonlineretoure/sandbox_user';
    const CONFIG_PATH_SANDBOX_USER_PASSWORD = 'shipping/dhlonlineretoure/sandbox_signature';
    const CONFIG_PATH_SANDBOX_RECEIVER_ID = 'shipping/dhlonlineretoure/sandbox_receiver_id';
    const CONFIG_PATH_SANDBOX_EKP = 'shipping/dhlonlineretoure/sandbox_ekp';
    const CONFIG_PATH_SANDBOX_PARTICIPATION = 'shipping/dhlonlineretoure/sandbox_participation';

    const CONFIG_PATH_PRODUCTION_ENDPOINT = 'shipping/dhlonlineretoure/production_endpoint';
    const CONFIG_PATH_PRODUCTION_AUTH_USERNAME = 'shipping/dhlonlineretoure/production_auth_username';
    const CONFIG_PATH_PRODUCTION_AUTH_PASSWORD = 'shipping/dhlonlineretoure/production_auth_password';
    const CONFIG_PATH_PRODUCTION_USER_NAME = 'shipping/dhlonlineretoure/production_user';
    const CONFIG_PATH_PRODUCTION_USER_PASSWORD = 'shipping/dhlonlineretoure/production_signature';
    const CONFIG_PATH_PRODUCTION_EKP = 'shipping/dhlonlineretoure/production_ekp';
    const CONFIG_PATH_PRODUCTION_PARTICIPATION = 'shipping/dhlonlineretoure/production_participation';
    const CONFIG_PATH_PRODUCTION_RECEIVER_ID = 'shipping/dhlonlineretoure/production_receiver_id';

    const CONFIG_PATH_UNIT_OF_MEASUREMENT = 'shipping/dhlonlineretoure/weight_uom';

    /**
     * Check if online return is enabled.
     *
     * @param mixed $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('shipping/dhlonlineretoure/active', $store);
    }

    /**
     * Check if online return logging is enabled.
     *
     * fixme(nr): use or remove
     *
     * @param mixed $store
     * @return boolean
     */
    public function isLoggingEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig('shipping/dhlonlineretoure/logging_enabled', $store);
    }

    /**
     * Check if sandbox mode is enabled.
     *
     * @param mixed $store
     * @return bool
     */
    public function isSandboxModeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PATH_SANDBOX_MODE, $store);
    }

    /**
     * Obtain the webservice endpoint address (location).
     *
     * @param mixed $store
     * @return string
     */
    public function getEndpoint($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_ENDPOINT, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_ENDPOINT, $store);
    }

    /**
     * Obtain username for CIG authentication.
     *
     * The web service authentication username is the application ID used in the HTTP basic auth header.
     *
     * @param mixed $store
     * @return string
     */
    public function getWebserviceAuthUsername($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_AUTH_USERNAME, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_AUTH_USERNAME, $store);
    }

    /**
     * Obtain password for CIG authentication.
     *
     * The web service authentication password is the application token used in the HTTP basic auth header.
     *
     * @param mixed $store
     * @return string
     */
    public function getWebserviceAuthPassword($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_AUTH_PASSWORD, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_AUTH_PASSWORD, $store);
    }

    /**
     * Obtain user for DHL customer authentication.
     *
     * @param mixed $store
     * @return string
     */
    public function getUser($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_USER_NAME, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_USER_NAME, $store);
    }

    /**
     * Obtain password (signature) for DHL customer authentication.
     *
     * @param mixed $store
     * @return string
     */
    public function getSignature($store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            return Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_USER_PASSWORD, $store);
        }

        return Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_USER_PASSWORD, $store);
    }

    /**
     * Get receiver ID by ISO 3166 ALPHA-2 country ID.
     *
     * @link https://en.wikipedia.org/wiki/ISO_3166-1
     *
     * @param string $countryCode
     * @param mixed $store
     * @return string Receiver ID if available for given country, empty string otherwise.
     */
    public function getReceiverId($countryCode, $store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            $receiverIds = Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_RECEIVER_ID, $store);
        } else {
            $receiverIds = Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_RECEIVER_ID, $store);
        }

        if (!is_array($receiverIds) || empty($countryCode)) {
            return '';
        }

        $receiverIds = array_column($receiverIds, 'name', 'iso');

        return isset($receiverIds[$countryCode]) ? $receiverIds[$countryCode] : '';
    }

    /**
     * Get the billing number for the given sender country.
     *
     * @param string $shipperCountry
     * @param mixed $store
     * @return string
     */
    public function getBillingNumber($shipperCountry, $store = null)
    {
        if ($this->isSandboxModeEnabled($store)) {
            $ekp = Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_EKP, $store);
            $participationNumbers = Mage::getStoreConfig(self::CONFIG_PATH_SANDBOX_PARTICIPATION, $store);
        } else {
            $ekp = Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_EKP, $store);
            $participationNumbers = Mage::getStoreConfig(self::CONFIG_PATH_PRODUCTION_PARTICIPATION, $store);
        }

        if ($shipperCountry === 'DE') {
            $procedure = Procedure::PROCEDURE_RETURNSHIPMENT_NATIONAL;
        } else {
            $procedure = Procedure::PROCEDURE_RETURNSHIPMENT_INTERNATIONAL;
        }

        $participationNumbers = array_column($participationNumbers, 'participation', 'procedure');
        $participationNumber = isset($participationNumbers[$procedure]) ? $participationNumbers[$procedure] : '';

        return $ekp . $procedure . $participationNumber;
    }

    /**
     * Obtain CMS page url key
     *
     * @param mixed $store
     * @return string
     */
    public function getCmsRevocationPage($store = null)
    {
        $page = Mage::getStoreConfig('shipping/dhlonlineretoure/cms_revocation_page', $store);
        if (!$page) {
            return '';
        }

        return $page;
    }

    /**
     * Obtain catalog weight unit.
     *
     * @param mixed $store
     * @return string
     */
    public function getWeightUnit($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_UNIT_OF_MEASUREMENT, $store);
    }

    /**
     * Get allowed shipping methods.
     *
     * If for example "flatrate_flatrate" is selected in config, then "dhlversenden_flatrate" is also added.
     *
     * @param mixed $store
     * @return string[]
     */
    public function getAllowedShippingMethods($store = null)
    {
        $originalMethods = Mage::getStoreConfig('shipping/dhlonlineretoure/allowed_shipping_methods', $store);
        $originalMethods = explode(',', $originalMethods);

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
     * Check if shipping method is allowed.
     *
     * @param string $shippingMethod
     * @param mixed $store
     * @return boolean
     */
    public function isAllowedShippingMethod($shippingMethod, $store = null)
    {
        $allowedShippingMethods = $this->getAllowedShippingMethods($store);

        return in_array($shippingMethod, $allowedShippingMethods);
    }

    /**
     * Check if the given country code identifies a country in the EU.
     *
     * @param string $countryCode
     * @return bool
     */
    public function isEuCountry($countryCode)
    {
        $euCountries = explode(',', Mage::getStoreConfig('general/country/eu_countries'));
        return in_array($countryCode, $euCountries, true);
    }
}
