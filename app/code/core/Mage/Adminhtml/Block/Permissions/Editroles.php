<?php
class Mage_Adminhtml_Block_Permissions_Editroles extends Mage_Adminhtml_Block_Widget_Tabs {   
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
    }
    
    protected function _beforeToHtml()
    {
    	$role = Mage::getModel("permissions/roles")
    	   ->load($this->_request->getParam('id', false));
    	
    	$this->addTab('info', array(
            'label'     => __('Role Info'),
            'title'     => __('Role Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_roleinfo')->setRole($role)->toHtml(),
            'active'    => true
        ));
        
        $this->addTab('account', array(
            'label'     => __('Roles Resources'),
            'title'     => __('Roles Resources'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_rolesedit')->toHtml(),            
        ));

        $this->addTab('roles', array(
            'label'     => __('Users Roles'),
            'title'     => __('Users Roles'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_rolesusers')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}