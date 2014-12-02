<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Review;

class Customer extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    /**
     * Customer Reviews Report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Magento_Review::report_review_customer'
        )->_addBreadcrumb(
            __('Customers Report'),
            __('Customers Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Reviews Report'));
        $this->_view->renderLayout();
    }
}
