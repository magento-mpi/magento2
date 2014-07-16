<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Auth;

class Logout extends \Magento\Backend\Controller\Adminhtml\Auth
{
    /**
     * Administrator logout action
     *
     * @return void
     */
    public function execute()
    {
        $this->_auth->logout();
        $this->messageManager->addSuccess(__('You have logged out.'));
        $this->getResponse()->setRedirect($this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl());
    }
}
