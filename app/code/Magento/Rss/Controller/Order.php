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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param \Magento\Core\Model\Config\Scope $configScope
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        \Magento\Core\Model\Config\Scope $configScope
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    public function preDispatch()
    {
        if ('new' === $this->getRequest()->getActionName()) {
            $this->_configScope->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
            if (!$this->authenticateAndAuthorizeAdmin('Magento_Sales::sales_order')) {
                return;
            }
        }
        parent::preDispatch();
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
        $order = $this->_objectManager->get('Magento_Rss_Helper_Order')
            ->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));

        if (!is_null($order)) {
            $this->_coreRegistry->register('current_order', $order);
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }

        $this->_forward('nofeed', 'index', 'rss');
    }
}
