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
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Config_Scope $configScope
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Config_Scope $configScope
    ) {
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    public function preDispatch()
    {
        if ('new' === $this->getRequest()->getActionName()) {
            $this->_configScope->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
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
            Mage::register('current_order', $order);
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }

        $this->_forward('nofeed', 'index', 'rss');
    }
}
