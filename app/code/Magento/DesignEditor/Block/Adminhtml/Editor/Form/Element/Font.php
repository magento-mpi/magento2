<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display composite font element for VDE
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'font';

    /**
     * Add form elements
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font
     */
    protected function _addFields()
    {
        $fontData = $this->getComponent('font-picker');
        $colorData = $this->getComponent('color-picker');

        $fontHtmlId = $this->getComponentId('font-picker');
        $fontTitle = $this->_escape(sprintf("%s {%s: %s}",
            $fontData['selector'],
            $fontData['attribute'],
            $fontData['value']
        ));
        $this->addField($fontHtmlId, 'font-picker', array(
            'name'    => $fontHtmlId,
            'value'   => $fontData['value'],
            'title'   => $fontTitle,
            'options' => array_combine($fontData['options'], $fontData['options']),
            'label'   => null,
        ));

        $colorTitle = $this->_escape(sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        ));
        $colorHtmlId = $this->getComponentId('color-picker');
        $this->addField($colorHtmlId, 'color-picker', array(
            'name'  => $colorHtmlId,
            'value' => $colorData['value'],
            'title' => $colorTitle,
            'label' => null,
        ));

        return $this;
    }

    /**
     * Add element types used in composite font element
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font
     */
    protected function _addElementTypes()
    {
        $this->addType('color-picker', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('font-picker', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker');

        return $this;
    }
}
