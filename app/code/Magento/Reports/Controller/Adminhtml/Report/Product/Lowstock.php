<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Product;

class Lowstock extends \Magento\Reports\Controller\Adminhtml\Report\Product
{
    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::lowstock');
    }

    /**
     * Low stock action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Low Stock Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_products_lowstock'
        )->_addBreadcrumb(
            __('Low Stock'),
            __('Low Stock')
        );
        $this->_view->renderLayout();
    }
}
