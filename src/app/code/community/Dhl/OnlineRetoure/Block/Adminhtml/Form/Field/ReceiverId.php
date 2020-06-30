<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl OnlineRetoure receiver id combined form field (frontend model).
 *
 * @see template/system/config/form/field/array.phtml
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Adminhtml_Form_Field_ReceiverId
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
            $sourceModel = Mage::getModel('adminhtml/system_config_source_country');

            $this->_templateRenderer = $this->getLayout()->createBlock(
                'dhlonlineretoure/adminhtml_form_field_selects_select',
                '',
                array(
                    'is_render_to_js_template' => true,
                    'class' => 'select',
                    'title' => Mage::helper('dhlonlineretoure/data')->__('Select Country'),
                )
            );
            $this->_templateRenderer->setOptions($sourceModel->toOptionArray());
        }

        return $this->_templateRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTemplateRenderer()->calcOptionHash($row->getData('iso')),
            'selected="selected"'
        );

        return parent::_prepareArrayRow($row);
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'iso',
            array(
                'label' => $this->__('Country'),
                'renderer' => $this->_getTemplateRenderer()
            )
        );
        $this->addColumn(
            'name',
            array(
                'label' => $this->__('Receiver ID'),
                'style' => 'width:100px',
            )
        );

        // hide "Add after" button
        $this->_addAfter = false;

        return parent::_prepareToRender();
    }
}
