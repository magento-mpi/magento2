<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment;

class Detail extends \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
{
    /**
     * Detail Action of customer segment
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Customer Segment Report'));

        if ($this->_initSegment()) {
            // Add help Notice to Combined Report
            if ($this->_getAdminSession()->getMassactionIds()) {
                $collection = $this->_collectionFactory->create()->addFieldToFilter(
                    'segment_id',
                    array('in' => $this->_getAdminSession()->getMassactionIds())
                );

                $segments = array();
                foreach ($collection as $item) {
                    $segments[] = $item->getName();
                }
                /* @translation __('Viewing combined "%1" report from segments: %2') */
                if ($segments) {
                    $viewModeLabel = $this->_objectManager->get(
                        'Magento\CustomerSegment\Helper\Data'
                    )->getViewModeLabel(
                        $this->_getAdminSession()->getViewMode()
                    );
                    $this->messageManager->addNotice(
                        __('Viewing combined "%1" report from segments: %2.', $viewModeLabel, implode(', ', $segments))
                    );
                }
            }

            $this->_title->add(__('Details'));

            $this->_initAction();
            $this->_view->renderLayout();
        } else {
            $this->_redirect('*/*/segment');
            return;
        }
    }
}
