<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rss_Controller_Order extends Magento_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        if ('new' === $this->getRequest()->getActionName()) {
            $this->setCurrentArea('adminhtml');
            if (!self::authenticateAndAuthorizeAdmin($this, 'Mage_Sales::sales_order')) {
                return;
            }
        }
        parent::preDispatch();
    }

    /**
     * Check if admin is logged in and authorized to access resource by specified ACL path
     *
     * If not authenticated, will try to do it using credentials from HTTP-request
     *
     * @param Magento_Core_Controller_Front_Action $controller
     * @param string $aclResource
     * @return bool
     */
    public static function authenticateAndAuthorizeAdmin(Magento_Core_Controller_Front_Action $controller, $aclResource)
    {
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
        /** @var $auth Mage_Backend_Model_Auth */
        $auth = Mage::getModel('Mage_Backend_Model_Auth');
        $session = $auth->getAuthStorage();

        // try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = Mage::helper('Magento_Core_Helper_Http')
                ->getHttpAuthCredentials($controller->getRequest());
            try {
                $auth->login($login, $password);
            } catch (Mage_Backend_Model_Auth_Exception $e) {
                Mage::logException($e);
            }
        }

        // verify if logged in and authorized
        if (!$session->isLoggedIn() || !Mage::getSingleton('Magento_AuthorizationInterface')->isAllowed($aclResource)) {
            Mage::helper('Magento_Core_Helper_Http')->failHttpAuthentication($controller->getResponse(), 'RSS Feeds');
            $controller->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        return true;
    }

    public function newAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Order status action
     */
    public function statusAction()
    {
        $order = Mage::helper('Mage_Rss_Helper_Order')->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));
        if (!is_null($order)) {
            Mage::register('current_order', $order);
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }
        $this->_forward('nofeed', 'index', 'rss');
    }
}
