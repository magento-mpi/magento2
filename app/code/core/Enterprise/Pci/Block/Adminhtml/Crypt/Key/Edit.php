<?php
class Enterprise_Pci_Block_Adminhtml_Crypt_Key_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = null;
    protected $_controller = 'crypt_key';

    public function __construct()
    {
        Varien_Object::__construct();
        $this->_addButton('save', array(
            'label'     => Mage::helper('adminhtml')->__('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), 1);
    }

    public function getHeaderText()
    {
        return Mage::helper('enterprise_pci')->__('Change encryption key');
    }
}
