<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Select
 *
 * @package Dhl_OnlineRetoure
 * @link      https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Adminhtml_Form_Field_Selects_Select
    extends Mage_Adminhtml_Block_Html_Select
{
    protected function _construct()
    {
        $this
            ->setClass('select');
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
