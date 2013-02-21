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
 * Form element renderer to display color picker element for VDE
 *
 * @method string getValue()
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker extends Varien_Data_Form_Element_Abstract
{
    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->setCssClass('element-color-picker');
    }
}
