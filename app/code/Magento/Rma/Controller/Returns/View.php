<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Returns;

use Magento\Rma\Model\Rma;

class View extends \Magento\Rma\Controller\Returns
{
    /**
     * RMA view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/history');
            return;
        }
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create(
            'Magento\Sales\Model\Order'
        )->load(
            $this->_coreRegistry->registry('current_rma')->getOrderId()
        );
        $this->_coreRegistry->register('current_order', $order);

        $this->_view->loadLayout();
        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(
            __('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId())
        );

        $this->_view->renderLayout();
    }
}
