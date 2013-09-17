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
 * Form element renderer to display color picker element for VDE
 *
 * @method string getValue()
 * @method string getExtType()
 * @method string getCssClass()
 * @method string getRequired()
 * @method string getNote()
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker setCssClass($class)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker extends Magento_Data_Form_Element_Abstract
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'color-picker';

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->setCssClass('element-' . self::CONTROL_TYPE);
    }
}
