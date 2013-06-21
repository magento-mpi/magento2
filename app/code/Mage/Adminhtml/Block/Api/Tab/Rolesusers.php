<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Api_Tab_Rolesusers extends Mage_Adminhtml_Block_Widget_Tabs {

    protected function _construct()
    {
        parent::_construct();

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
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Api_Role_Grid_User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
