<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment;

class CustomerGrid extends \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
{
    /**
     * Segment customer ajax grid action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_initSegment(false)) {
            return;
        }
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
