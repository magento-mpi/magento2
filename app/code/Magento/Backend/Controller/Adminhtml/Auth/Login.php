<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Auth;

class Login extends \Magento\Backend\Controller\Adminhtml\Auth
{
    /**
     * Administrator login action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_auth->isLoggedIn()) {
            if ($this->_auth->getAuthStorage()->isFirstPageAfterLogin()) {
                $this->_auth->getAuthStorage()->setIsFirstPageAfterLogin(true);
            }
            $this->_redirect($this->_backendUrl->getStartupPageUrl());
            return;
        }
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
