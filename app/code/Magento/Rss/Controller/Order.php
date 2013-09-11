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

namespace Magento\Rss\Controller;

class Order extends \Magento\Core\Controller\Front\Action
{
    /**
     * @var \Magento\Core\Model\Config\Scope
     */
    protected $_configScope;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Config\Scope $configScope
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Config\Scope $configScope
    ) {
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    public function preDispatch()
    {
        if ('new' === $this->getRequest()->getActionName()) {
            $this->_configScope->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
            if (!self::authenticateAndAuthorizeAdmin($this, 'Magento_Sales::sales_order')) {
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
     * @param \Magento\Core\Controller\Front\Action $controller
     * @param string $aclResource
     * @return bool
     */
    public static function authenticateAndAuthorizeAdmin(\Magento\Core\Controller\Front\Action $controller, $aclResource)
    {
        \Mage::app()->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML, \Magento\Core\Model\App\Area::PART_CONFIG);
        /** @var $auth \Magento\Backend\Model\Auth */
        $auth = \Mage::getModel('\Magento\Backend\Model\Auth');
        $session = $auth->getAuthStorage();

        // try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = \Mage::helper('Magento\Core\Helper\Http')
                ->getHttpAuthCredentials($controller->getRequest());
            try {
                $auth->login($login, $password);
            } catch (\Magento\Backend\Model\Auth\Exception $e) {
                \Mage::logException($e);
            }
        }

        // verify if logged in and authorized
        if (!$session->isLoggedIn() || !\Mage::getSingleton('Magento\AuthorizationInterface')->isAllowed($aclResource)) {
            \Mage::helper('Magento\Core\Helper\Http')->failHttpAuthentication($controller->getResponse(), 'RSS Feeds');
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
        $order = \Mage::helper('Magento\Rss\Helper\Order')->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));
        if (!is_null($order)) {
            \Mage::register('current_order', $order);
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }
        $this->_forward('nofeed', 'index', 'rss');
    }
}
