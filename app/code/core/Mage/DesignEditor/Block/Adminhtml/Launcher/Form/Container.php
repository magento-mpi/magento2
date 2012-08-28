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
 * Design editor launcher form container
 */
class Mage_DesignEditor_Block_Adminhtml_Launcher_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Prevent dynamic form block creation, use the layout declaration instead
     *
     * @var string
     */
    protected $_mode = '';

    /**
     * Customize inherited buttons
     */
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('Mage_DesignEditor_Helper_Data')->__('Launch'));
        $this->_updateButton('save', 'region', 'footer');
    }

    /**
     * Retrieve appropriate text for the header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_DesignEditor_Helper_Data')->__('Visual Design Editor');
    }
}
