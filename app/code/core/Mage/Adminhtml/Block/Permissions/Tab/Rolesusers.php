<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Permissions_Tab_Rolesusers extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct()
    {
        parent::__construct();

        $roleId = $this->getRequest()->getParam('rid', false);

        $users = Mage::getModel('Mage_Admin_Model_User')->getCollection()->load();
        $this->setTemplate('permissions/rolesusers.phtml')
            ->assign('users', $users->getItems())
            ->assign('roleId', $roleId);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'userGrid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Permissions_Role_Grid_User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    protected function _getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
