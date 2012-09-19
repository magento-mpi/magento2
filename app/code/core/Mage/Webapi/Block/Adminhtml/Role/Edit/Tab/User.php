<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User extends Mage_Backend_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $roleId = $this->getRequest()->getParam('rid', false);

        $users = Mage::getModel('Mage_Api_Model_User')->getCollection()->load();
        $this->setTemplate('api/rolesusers.phtml')
            ->assign('users', $users->getItems())
            ->assign('roleId', $roleId);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'userGrid',
            $this->getLayout()->createBlock('Mage_Webapi_Block_Adminhtml_Role_Grid_User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    protected function _getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
