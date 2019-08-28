<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * DHL OnlineRetoure country select form field
 *
 * @category Dhl
 * @package  Dhl_OnlineRetoure
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Adminhtml_Form_Field_Country_Select
    extends Mage_Core_Block_Html_Select
{
    protected function _construct()
    {
        $this
            ->setClass('select')
            ->setTitle(Mage::helper('dhlonlineretoure')->__('Select country'));
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
