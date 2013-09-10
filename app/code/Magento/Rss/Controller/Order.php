<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rss_Controller_Order extends Magento_Core_Controller_Front_Action
{
    /**
     * @var Magento_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Config_Scope $configScope
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Config_Scope $configScope
    ) {
        $this->_configScope = $configScope;
        $this->_logger = $context->getLogger();
        parent::__construct($context);
    }

    public function preDispatch()
    {
        if ('new' === $this->getRequest()->getActionName()) {
            $this->_configScope->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
            if (!self::authenticateAndAuthorizeAdmin($this, 'Magento_Sales::sales_order', $this->_logger)) {
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
    public static function authenticateAndAuthorizeAdmin(Magento_Core_Controller_Front_Action $controller, $aclResource, $logger)
    {
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
        /** @var $auth Magento_Backend_Model_Auth */
        $auth = Mage::getModel('Magento_Backend_Model_Auth');
        $session = $auth->getAuthStorage();

        // try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = Mage::helper('Magento_Core_Helper_Http')
                ->getHttpAuthCredentials($controller->getRequest());
            try {
                $auth->login($login, $password);
            } catch (Magento_Backend_Model_Auth_Exception $e) {
                $logger->logException($e);
                //$controller->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
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
        $order = Mage::helper('Magento_Rss_Helper_Order')->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));
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
