<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Block_Role_Tab_Users extends Mage_Backend_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();

        $roleId = $this->getRequest()->getParam('rid', false);

        $users = Mage::getModel('Mage_User_Model_User')->getCollection()->load();
        $this->setTemplate('role/users.phtml')
            ->assign('users', $users->getItems())
            ->assign('roleId', $roleId);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'userGrid',
            $this->getLayout()->createBlock('Mage_User_Block_Role_Grid_User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
