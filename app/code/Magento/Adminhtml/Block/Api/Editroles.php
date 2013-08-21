<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Api_Editroles extends Magento_Adminhtml_Block_Widget_Tabs {
    protected function _construct()
    {
        parent::_construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role-edit-form');
        $this->setTitle(__('Role Information'));
    }

    protected function _beforeToHtml()
    {
        $roleId = $this->getRequest()->getParam('rid', false);
        $role = Mage::getModel('Magento_Api_Model_Roles')
           ->load($roleId);

        $this->addTab('info', array(
            'label'     => __('Role Info'),
            'title'     => __('Role Info'),
            'content'   => $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Api_Tab_Roleinfo'
            )->setRole($role)->toHtml(),
            'active'    => true
        ));

        $this->addTab('account', array(
            'label'     => __('Role Resources'),
            'title'     => __('Role Resources'),
            'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Api_Tab_Rolesedit')->toHtml(),
        ));

        if( intval($roleId) > 0 ) {
            $this->addTab('roles', array(
                'label'     => __('Role Users'),
                'title'     => __('Role Users'),
                'content'   => $this->getLayout()->createBlock(
                    'Magento_Adminhtml_Block_Api_Tab_Rolesusers',
                    'role.users.grid'
                )->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}
