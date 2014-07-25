<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;

class View extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * View order detail
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Orders'));

        $order = $this->_initOrder();
        if ($order) {
            try {
                $this->_initAction();
            } catch (\Magento\Framework\App\Action\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('sales/order/index');
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('Exception occurred during order load'));
                $this->_redirect('sales/order/index');
                return;
            }
            $this->_title->add(sprintf("#%s", $order->getRealOrderId()));
            $this->_view->renderLayout();
        }
    }
}
