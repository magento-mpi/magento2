<?php
class Mage_Adminhtml_Block_Permissions_Edituser extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('user_edit_form');
    }

    protected function _beforeToHtml()
    {
    	$user = Mage::getModel("permissions/users")->load($this->_request->getParam('id', false));

        $this->addTab('account', array(
            'label'     => __('User Info'),
            'title'     => __('User Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_useredit')->setUser($user)->toHtml(),
            'active'    => true
        ));
        if( $this->getUserId() ) {
            $this->addTab('roles', array(
                'label'     => __('Roles'),
                'title'     => __('Roles'),
                'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_userroles')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}
