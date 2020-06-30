<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Model_Adminhtml_System_Config_Source_Procedure
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Adminhtml_System_Config_Source_Procedure
{
    const PROCEDURE_RETURNSHIPMENT_NATIONAL = '07';
    const PROCEDURE_RETURNSHIPMENT_INTERNATIONAL = '53';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();

        $options = $this->toArray();
        foreach ($options as $value => $label) {
            $optionArray[]= array('value' => $value, 'label' => $label);
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $helper = Mage::helper('dhlonlineretoure/data');
        return array(
            self::PROCEDURE_RETURNSHIPMENT_NATIONAL => $helper->__('Retoure DHL Paket National'),
            self::PROCEDURE_RETURNSHIPMENT_INTERNATIONAL => $helper->__('Retoure DHL Paket International'),
        );
    }
}
