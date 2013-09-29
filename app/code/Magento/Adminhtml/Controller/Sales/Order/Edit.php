<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order edit controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Sales\Order;

class Edit extends \Magento\Adminhtml\Controller\Sales\Order\Create
{
    /**
     * Start edit order initialization
     */
    public function startAction()
    {
        $this->_getSession()->clear();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

        try {
            if ($order->getId()) {
                $this->_getSession()->setUseOldShippingMethod(true);
                $this->_getOrderCreateModel()->initFromOrder($order);
                $this->_redirect('*/*');
            }
            else {
                $this->_redirect('*/sales_order/');
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addException($e, $e->getMessage());
            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
        }
    }

    /**
     * Index page
     */
    public function indexAction()
    {
        $this->_title(__('Orders'))->_title(__('Edit Order'));
        $this->loadLayout();

        $this->_initSession()
            ->_setActiveMenu('Magento_Sales::sales_order')
            ->renderLayout();
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::actions_edit');
    }
}
