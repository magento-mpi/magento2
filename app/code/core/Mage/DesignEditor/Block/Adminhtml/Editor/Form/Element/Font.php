<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display composite font element for VDE
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    const CONTROL_TYPE = 'font';

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->addElementTypes();
        $this->addFields();

        $this->addClass('element-' . self::CONTROL_TYPE);
    }

    /**
     * Add form elements
     */
    public function addFields()
    {
        $components = $this->getComponents();
        $fontData = $components['store-name:font-picker'];
        $colorData = $components['store-name:color-picker'];

        $fontHtmlId = uniqid('font-picker-');
        $fontTitle = sprintf("%s {%s: %s}",
            $fontData['selector'],
            $fontData['attribute'],
            $fontData['value']
        );
        $this->addField($fontHtmlId, 'font-picker', array(
            'name'    => $fontHtmlId,
            'value'   => $fontData['value'],
            'title'   => $fontTitle,
            'options' => array_combine($fontData['options'], $fontData['options']),
            'label'   => null,
        ));

        $colorTitle = sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        );
        $colorHtmlId = uniqid('color-picker-');
        $this->addField($colorHtmlId, 'color-picker', array(
            'name'  => $colorHtmlId,
            'value' => $colorData['value'],
            'title' => $colorTitle,
            'class' => '123class',
            'css_class' => '456class',
            'label' => null,
        ));
    }

    /**
     * Add element types used in composite font element
     */
    public function addElementTypes()
    {
        $this->addType('color-picker', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('font-picker', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker');
    }
}
