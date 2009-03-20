<?php
class Enterprise_Pci_Block_Adminhtml_Locks extends Mage_Adminhtml_Block_Widget_Container
{
    public function getHeaderText()
    {
        return Mage::helper('enterprise_pci')->__('Locked administrators');
    }

    public function getGridHtml()
    {
        return $this->getLayout()->createBlock('enterprise_pci/adminhtml_locks_grid')->toHtml();
    }
}
