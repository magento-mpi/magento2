<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Auth backend controller
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Controller\Adminhtml;

class Auth extends \Magento\Backend\Controller\ActionAbstract
{
    /**
     * Administrator login action
     */
    public function loginAction()
    {
        $session = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        if ($session->isLoggedIn()) {
            if ($session->isFirstPageAfterLogin()) {
                $session->setIsFirstPageAfterLogin(true);
            }
            $this->_redirect(\Mage::getSingleton('Magento\Backend\Model\Url')->getStartupPageUrl());
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Administrator logout action
     */
    public function logoutAction()
    {
        $auth = \Mage::getSingleton('Magento\Backend\Model\Auth');
        $auth->logout();
        $auth->getAuthStorage()->addSuccess(__('You have logged out.'));
        $this->getResponse()->setRedirect($this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl());
    }

    /**
     * Denied JSON action
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
     */
    public function deniedIframeAction()
    {
        $this->getResponse()->setBody($this->_getDeniedIframe());
    }

    /**
     * Retrieve response for deniedIframeAction()
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
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
