<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Product;

use \Magento\Reports\Model\Flag;

class Viewed extends \Magento\Reports\Controller\Adminhtml\Report\Product
{
    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::viewed');
    }

    /**
     * Most viewed products
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Product Views Report'));

        $this->_showLastExecutionTime(Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE, 'viewed');

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_products_viewed'
        )->_addBreadcrumb(
            __('Products Most Viewed Report'),
            __('Products Most Viewed Report')
        );

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_product_viewed.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->_view->renderLayout();
    }
}
