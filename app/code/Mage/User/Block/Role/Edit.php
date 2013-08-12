<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Block_Role_Edit extends Magento_Backend_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role-edit-form');
        $this->setTitle(Mage::helper('Mage_User_Helper_Data')->__('Role Information'));
    }

    protected function _prepareLayout()
    {
        $role = Mage::registry('current_role');

        $this->addTab(
            'info',
            $this->getLayout()
                ->createBlock('Mage_User_Block_Role_Tab_Info')
                ->setRole($role)
                ->setActive(true)
        );

        if ($role->getId()) {
            $this->addTab('roles', array(
                'label'     => Mage::helper('Mage_User_Helper_Data')->__('Role Users'),
                'title'     => Mage::helper('Mage_User_Helper_Data')->__('Role Users'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_User_Block_Role_Tab_Users', 'role.users.grid')
                    ->toHtml(),
            ));
        }

        return parent::_prepareLayout();
    }
}
