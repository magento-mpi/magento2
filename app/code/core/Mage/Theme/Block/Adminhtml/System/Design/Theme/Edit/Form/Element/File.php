<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display file element
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File extends Varien_Data_Form_Element_File
{
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
