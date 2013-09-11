<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Api;

class Editroles extends \Magento\Adminhtml\Block\Widget\Tabs {
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
        $role = \Mage::getModel('Magento\Api\Model\Roles')
           ->load($roleId);

        $this->addTab('info', array(
            'label'     => __('Role Info'),
            'title'     => __('Role Info'),
            'content'   => $this->getLayout()->createBlock(
                '\Magento\Adminhtml\Block\Api\Tab\Roleinfo'
            )->setRole($role)->toHtml(),
            'active'    => true
        ));

        $this->addTab('account', array(
            'label'     => __('Role Resources'),
            'title'     => __('Role Resources'),
            'content'   => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Api\Tab\Rolesedit')->toHtml(),
        ));

        if( intval($roleId) > 0 ) {
            $this->addTab('roles', array(
                'label'     => __('Role Users'),
                'title'     => __('Role Users'),
                'content'   => $this->getLayout()->createBlock(
                    '\Magento\Adminhtml\Block\Api\Tab\Rolesusers',
                    'role.users.grid'
                )->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}
