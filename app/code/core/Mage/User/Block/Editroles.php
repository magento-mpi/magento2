<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Permissions_Editroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Information'));
    }

    protected function _prepareLayout()
    {
        $role = Mage::registry('current_role');

        $this->addTab(
            'info',
            $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Permissions_Tab_Roleinfo')
                ->setRole($role)
                ->setActive(true)
        );
        $this->addTab(
            'account',
            $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Permissions_Tab_Rolesedit', 'adminhtml.permissions.tab.rolesedit')
        );

        if ($role->getId()) {
            $this->addTab('roles', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Users'),
                'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Users'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Permissions_Tab_Rolesusers', 'role.users.grid')
                    ->toHtml(),
            ));
        }

        return parent::_prepareLayout();
    }
}
