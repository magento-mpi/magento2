<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Match extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Match segment customers action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initSegment();
            if ($model->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                $model->matchCustomers();
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customersegment/*/');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Segment Customers matching error'));
            $this->_redirect('customersegment/*/');
            return;
        }
        $this->_redirect('customersegment/*/edit', ['id' => $model->getId(), 'active_tab' => 'customers_tab']);
    }
}
