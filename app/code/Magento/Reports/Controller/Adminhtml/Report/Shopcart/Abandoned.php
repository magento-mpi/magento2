<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Shopcart;

class Abandoned extends \Magento\Reports\Controller\Adminhtml\Report\Shopcart
{
    /**
     * Abandoned carts action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Abandoned Carts'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_shopcart_abandoned'
        )->_addBreadcrumb(
            __('Abandoned Carts'),
            __('Abandoned Carts')
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Abandoned')
        );
        $this->_view->renderLayout();
    }
}
