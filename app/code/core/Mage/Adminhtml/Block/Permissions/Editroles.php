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
        $roleId = $this->_request->getParam('rid', false);
    	$role = Mage::getModel("permissions/roles")
    	   ->load($roleId);

    	$this->addTab('info', array(
            'label'     => __('Role Info'),
            'title'     => __('Role Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_roleinfo')->setRole($role)->toHtml(),
            'active'    => true
        ));

        $this->addTab('account', array(
            'label'     => __('Role Resources'),
            'title'     => __('Role Resources'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_rolesedit')->toHtml(),
        ));

        if( intval($roleId) > 0 ) {
            $this->addTab('roles', array(
                'label'     => __('Role Users'),
                'title'     => __('Role Users'),
                'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_rolesusers')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

    protected function _initChildren()
    {
        $this->setChild('backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/').'\''
                ))
        );

        $this->setChild('resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'window.location.reload()'
                ))
        );
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }
}