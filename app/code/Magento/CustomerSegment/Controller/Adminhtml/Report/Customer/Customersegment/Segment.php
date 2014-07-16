<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment;

class Segment extends \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
{
    /**
     * Segment Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Customer Segment Report'));

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
