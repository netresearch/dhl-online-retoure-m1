<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl OnlineRetoure participation numbers combined form field (frontend model).
 *
 * @see template/system/config/form/field/array.phtml
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Adminhtml_Form_Field_Participation
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Mage_Core_Block_Html_Select
     */
    protected $_templateRenderer;


    /**
     * Create renderer used for displaying the country select element
     *
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getTemplateRenderer()
    {
        if (!$this->_templateRenderer) {
            $sourceModel = Mage::getModel('dhlonlineretoure/adminhtml_system_config_source_procedure');

            $this->_templateRenderer = $this->getLayout()->createBlock(
                'dhlonlineretoure/adminhtml_form_field_selects_select',
                '',
                array(
                    'is_render_to_js_template' => true,
                    'class' => 'select',
                    'title' => Mage::helper('dhlonlineretoure/data')->__('Select Procedure'),
                )
            );

            $this->_templateRenderer->setOptions($sourceModel->toOptionArray());
        }

        return $this->_templateRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTemplateRenderer()->calcOptionHash($row->getData('procedure')),
            'selected="selected"'
        );

        parent::_prepareArrayRow($row);
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'procedure',
            array(
                'label' => $this->__('Procedure'),
                'renderer' => $this->_getTemplateRenderer()
            )
        );
        $this->addColumn(
            'participation',
            array(
                'label' => $this->__('Participation'),
                'style' => 'width:80px',
                'class' => 'input-text required-entry'
            )
        );
        // hide "Add after" button
        $this->_addAfter = false;

        return parent::_prepareToRender();
    }
}
