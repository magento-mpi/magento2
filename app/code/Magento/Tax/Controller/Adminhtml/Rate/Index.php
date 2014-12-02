<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class Index extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Show Main Grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Manage Tax Rates'), __('Manage Tax Rates'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Tax Zones and Rates'));
        $this->_view->renderLayout();
    }
}
