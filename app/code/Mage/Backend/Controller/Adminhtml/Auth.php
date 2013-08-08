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
class Mage_Backend_Controller_Adminhtml_Auth extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Administrator login action
     */
    public function loginAction()
    {
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if ($session->isLoggedIn()) {
            if ($session->isFirstPageAfterLogin()) {
                $session->setIsFirstPageAfterLogin(true);
            }
            $this->_redirect(Mage::getSingleton('Mage_Backend_Model_Url')->getStartupPageUrl());
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
        $this->getResponse()->setRedirect(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl());
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
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode(array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl()
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
            . Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl() . '\';</script>';
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
