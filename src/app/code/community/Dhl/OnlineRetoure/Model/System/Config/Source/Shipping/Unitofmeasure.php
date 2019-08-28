<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Dhl_OnlineRetoure_Model_System_Config_Source_Shipping_Unitofmeasure
 *
 * @category Dhl
 * @package  Dhl_OnlineRetoure
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_System_Config_Source_Shipping_Unitofmeasure
{
    public function toOptionArray()
    {
        $unitArr = array(
            'G'   =>  Mage::helper('dhlonlineretoure/data')->__('Grams'),
            'KG'  =>  Mage::helper('dhlonlineretoure/data')->__('Kilograms'),
        );

        $returnArr = array();
        foreach ($unitArr as $key => $val) {
            $returnArr[] = array(
                'value' => $key,
                'label' => $val,
            );
        }

        return $returnArr;
    }
}
