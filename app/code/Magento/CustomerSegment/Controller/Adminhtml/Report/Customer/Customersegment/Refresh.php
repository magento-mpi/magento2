<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment;

class Refresh extends \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
{
    /**
     * Apply segment conditions to all customers
     *
     * @return void
     */
    public function execute()
    {
        $segment = $this->_initSegment();
        if ($segment) {
            try {
                if ($segment->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                    $segment->matchCustomers();
                }
                $this->messageManager->addSuccess(__('Customer Segment data has been refreshed.'));
                $this->_redirect('*/*/detail', ['_current' => true]);
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/detail', ['_current' => true]);
        return;
    }
}
