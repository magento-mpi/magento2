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
 * Form element renderer to display file element for VDE
 *
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader setAccept($accept)
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader setMultiple(bool $isMultiple)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader extends Magento_Data_Form_Element_File
{
    //const CONTROL_TYPE = 'uploader';

    /**
     * Additional html attributes
     *
     * @var array
     */
    protected $_htmlAttributes = array('accept', 'multiple');

    /**
     * Html attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
