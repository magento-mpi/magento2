<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Auth backend controller
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Adminhtml_AuthController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $session = Mage::getSingleton('Mage_Admin_Model_Session');
        $url = $session->getUser()->getStartupPageUrl();
        if ($session->isFirstPageAfterLogin()) {
            // retain the "first page after login" value in session (before redirect)
            $session->setIsFirstPageAfterLogin(true);
        }
        $this->_redirect($url);
    }

    /**
     * Administrator login action
     */
    public function loginAction()
    {
        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isLoggedIn()) {
            $this->_redirect('adminhtml/auth/index');
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
        $auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $auth->logout();
        $auth->getAuthStorage()->addSuccess(Mage::helper('Mage_Backend_Helper_Data')->__('You have logged out.'));
        $this->_redirect('*');
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
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => $this->getUrl('*/auth/login')
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
            . $this->getUrl('*/auth/login') . '\';</script>';
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
