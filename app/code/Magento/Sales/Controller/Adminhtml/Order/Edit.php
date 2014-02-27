<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

/**
 * Adminhtml sales order edit controller
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Start edit order initialization
     *
     * @return void
     */
    public function startAction()
    {
        $this->_getSession()->clearStorage();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

        try {
            if ($order->getId()) {
                $this->_getSession()->setUseOldShippingMethod(true);
                $this->_getOrderCreateModel()->initFromOrder($order);
                $this->_redirect('sales/*');
            } else {
                $this->_redirect('sales/order/');
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('sales/order/view', array('order_id' => $orderId));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
            $this->_redirect('sales/order/view', array('order_id' => $orderId));
        }
    }

    /**
     * Index page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Orders'));
        $this->_title->add(__('Edit Order'));
        $this->_view->loadLayout();

        $this->_initSession()
            ->_setActiveMenu('Magento_Sales::sales_order');
        $this->_view->renderLayout();
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
