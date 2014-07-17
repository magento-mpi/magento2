<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Review;

class Product extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    /**
     * Product reviews report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Product Reviews Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Review::report_review_product'
        )->_addBreadcrumb(
            __('Products Report'),
            __('Products Report')
        );
        $this->_view->renderLayout();
    }
}
