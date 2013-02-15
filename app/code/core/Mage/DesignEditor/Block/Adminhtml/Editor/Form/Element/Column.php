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
 * Column renderer to Quick Styles panel in VDE
 *
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column setClass($class)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->addElementTypes();
        $this->setClass('column');
    }

    /**
     * Add element types used in composite font element
     */
    public function addElementTypes()
    {
        //contains composite font element and logo uploader
        $this->addType('logo', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo');

        //contains font picker, color picker
        $this->addType('font', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font');

        //contains color picker and bg uploader
        $this->addType('background', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background');

        $this->addType('color-picker', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('font-picker', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker');
        $this->addType('logo-uploader', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader');
        $this->addType('background-uploader', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader');

        //$this->addType('js_files', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_File');
    }
}

