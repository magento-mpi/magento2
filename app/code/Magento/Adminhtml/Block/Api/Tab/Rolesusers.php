<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Api\Tab;

class Rolesusers extends \Magento\Adminhtml\Block\Widget\Tabs {

    protected function _construct()
    {
        parent::_construct();

        $roleId = $this->getRequest()->getParam('rid', false);

        $users = \Mage::getModel('\Magento\Api\Model\User')->getCollection()->load();
        $this->setTemplate('api/rolesusers.phtml')
            ->assign('users', $users->getItems())
            ->assign('roleId', $roleId);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'userGrid',
            $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Api\Role\Grid\User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
