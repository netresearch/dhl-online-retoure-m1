<?php
/**
 * Dhl_OnlineRetoure_Helper_Data
 *
 * @package   Dhl_Account
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @copyright Copyright (c) 2012 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class Dhl_OnlineRetoure_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Check if the current installation is older than CE 1.7 / EE 1.12
     *
     * @return boolean
     */
    public function isLegacyInstallation()
    {
        $customerVersion = Mage::getConfig()->getModuleConfig('Mage_Customer')->version;
        return version_compare($customerVersion, '1.6.2', '<');
    }

    /**
     * Check if customer is logged in currently
     *
     * @see Mage_Customer_Helper_Data::isLoggedIn()
     * @return boolean
     */
    public function isCustomerLoggedIn()
    {
        if (Mage::helper('customer/data')->isLoggedIn()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get currently logged in customer
     *
     * @return Mage_Customer_Model_Customer
     * @see Mage_Customer_Helper_Data::getCustomer()
     */
    public function getLoggedInCustomer()
    {
        return Mage::helper('customer/data')->getCustomer();
    }

    /**
     * Get DHL Retoure Config
     *
     * @return Dhl_OnlineRetoure_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel("dhlonlineretoure/config");
    }

    /**
     * split street into street name, number and care of
     *
     * @param string $street
     *
     * @return array
     */
    public function splitStreet($street)
    {
        /*
         * first pattern  | street_name             | required | ([^0-9]+)         | all characters != 0-9
         * second pattern | additional street value | optional | ([0-9]+[ ])*      | numbers + white spaces
         * ignore         |                         |          | [ \t]*            | white spaces and tabs
         * second pattern | street_number           | optional | ([0-9]+[-\w^.]+)? | numbers + any word character
         * ignore         |                         |          | [, \t]*           | comma, white spaces and tabs
         * third pattern  | care_of                 | optional | ([^0-9]+.*)?      | all characters != 0-9 + any character except newline
         */
        if (preg_match("/^([^0-9]+)([0-9]+[ ])*[ \t]*([0-9]*[-\w^.]*)?[, \t]*([^0-9]+.*)?\$/", $street, $matches)) {

            //check if street has additional value and add it to streetname
            if (preg_match("/^([0-9]+)?\$/", trim($matches[2]))) {
                $matches[1] = $matches[1] . $matches[2];

            }
            return array(
                'street_name'   => trim($matches[1]),
                'street_number' => isset($matches[3]) ? $matches[3] : '',
                'care_of'       => isset($matches[4]) ? trim($matches[4]) : ''
            );
        }
        return array(
            'street_name'   => $street,
            'street_number' => '',
            'care_of'       => ''
        );
    }

    /**
     * Log to a separate log file
     *
     * @param string $message
     * @param int    $level
     * @param bool   $force
     * @return Dhl_OnlineRetoure_Helper_Data
     */
    public function log($message, $level=null, $force=false)
    {
        if (Mage::getStoreConfig('shipping/dhlonlineretoure/logging_enabled')) {
            $logfile = 'dhl_retoure.log';
            Mage::log($message, $level, $logfile, $force);
        }
        return $this;
    }
}