<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme editor container
 */
class Mage_Adminhtml_Block_System_Design_Theme_Edit extends Mage_Backend_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'Mage_Adminhtml';
        $this->_controller = 'System_Design_Theme';
        $this->setId('theme_edit');

        $this->_addButton('save_and_continue', array(
            'label'   => $this->__('Save and Continue Edit'),
            'onclick' => "editForm.submit($('edit_form').action+'back/edit/');",
            'class'   => 'save',
        ), 1);
    }

    /**
     * Prepare header for container
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('theme')->getId()) {
            $header = $this->__('Edit Theme');
        } else {
            $header = $this->__('New Theme');
        }
        return $header;
    }
}
