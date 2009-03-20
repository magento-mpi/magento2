<?php
class Enterprise_Pci_Block_Adminhtml_Locks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getCollection()
    {
        $this->_collection = Mage::getResourceModel('admin/user_collection');
        return $this->_collection;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
                'header' => Mage::helper('enterprise_pci')->__('ID'),
                'index'  => 'user_id',
                'width'  => 50,
                'filter' => false,
        ));
        $this->addColumn('username', array(
                'header' => Mage::helper('enterprise_pci')->__('Username'),
                'index'  => 'username',
        ));
        $this->addColumn('lock_expires', array(
                'header' => Mage::helper('enterprise_pci')->__('Lock expires'),
                'index'  => 'lock_expires',
                'filter' => false,
        ));
        $this->addColumn('unlock', array(
                'header' => Mage::helper('enterprise_pci')->__('Unlock'),
                'width' => 50,
                'filter' => false,
                'sortable' => false,
        ));

/*
firstname,lastname,email,username,password,created,modified,logdate,lognum,reload_acl_flag,is_active,extra,failed_login_attempts,lock_expires
*/
        return parent::_prepareColumns();
    }
}
