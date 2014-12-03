<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Adminhtml\Paypal\Reports;

class Details extends \Magento\Paypal\Controller\Adminhtml\Paypal\Reports
{
    /**
     * View transaction details action
     *
     * @return void
     */
    public function execute()
    {
        $rowId = $this->getRequest()->getParam('id');
        $row = $this->_rowFactory->create()->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_coreRegistry->register('current_transaction', $row);
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('View Transaction'));
        $this->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Paypal\Block\Adminhtml\Settlement\Details',
                'settlementDetails'
            )
        );
        $this->_view->renderLayout();
    }
}
