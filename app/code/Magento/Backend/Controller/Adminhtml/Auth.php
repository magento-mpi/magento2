<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\App\AbstractAction;

/**
 * Auth backend controller
 */
class Auth extends AbstractAction
{
    /**
     * Administrator login action
     *
     * @return void
     */
    public function loginAction()
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

    /**
     * Administrator logout action
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->_auth->logout();
        $this->messageManager->addSuccess(__('You have logged out.'));
        $this->getResponse()->setRedirect($this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl());
    }

    /**
     * Denied JSON action
     *
     * @return void
     */
    public function deniedJsonAction()
    {
        $this->getResponse()->setBody($this->_getDeniedJson());
    }

    /**
     * Retrieve response for deniedJsonAction()
     *
     * @return string
     */
    protected function _getDeniedJson()
    {
        return $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => $this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl()
        ));
    }

    /**
     * Denied IFrame action
     *
     * @return void
     */
    public function deniedIframeAction()
    {
        $this->getResponse()->setBody($this->_getDeniedIframe());
    }

    /**
     * Retrieve response for deniedIframeAction()
     *
     * @return string
     */
    protected function _getDeniedIframe()
    {
        return '<script type="text/javascript">parent.window.location = \''
            . $this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl() . '\';</script>';
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
