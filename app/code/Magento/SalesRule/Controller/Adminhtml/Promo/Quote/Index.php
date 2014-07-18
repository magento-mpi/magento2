<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Controller\Adminhtml\Promo\Quote;

class Index extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Cart Price Rules'));

        $this->_initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->renderLayout();
    }
}
