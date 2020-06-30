<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Helper_Data
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Helper_Customer_Address extends Mage_Core_Helper_Abstract
{
    /**
     * Version switch for legacy installations.
     *
     * @see Mage_Customer_Helper_Address::getAttributeValidationClass()
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeValidationClass($attributeCode)
    {
        if (Mage::helper('dhlonlineretoure/data')->isLegacyInstallation()) {
            return '';
        }

        return Mage::helper('customer/address')->getAttributeValidationClass($attributeCode);
    }

    /**
     * Version switch for legacy installations.
     *
     * @see Mage_Customer_Helper_Address::isVatAttributeVisible()
     * @return boolean
     */
    public function isVatAttributeVisible()
    {
        if (Mage::helper('dhlonlineretoure/data')->isLegacyInstallation()) {
            return false;
        }

        return Mage::helper('customer/address')->isVatAttributeVisible();
    }
}
