<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class RmaOrder extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Generate RMA grid for ajax request from order page
     *
     * @return void
     */
    public function execute()
    {
        $orderId = intval($this->getRequest()->getParam('order_id'));
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\Rma\Block\Adminhtml\Order\View\Tab\Rma'
            )->setOrderId(
                $orderId
            )->toHtml()
        );
    }
}
