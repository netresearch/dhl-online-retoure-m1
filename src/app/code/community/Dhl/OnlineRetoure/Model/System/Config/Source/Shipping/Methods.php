<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * DHL OnlineRetoure Allowed shipping methods for email return block
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_System_Config_Source_Shipping_Methods
    extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods
{
    /**
     * Get payment methods.
     *
     * @return array $methods
     */
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        return parent::toOptionArray(true);
    }
}
