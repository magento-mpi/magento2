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
        $this->_title->add(__('Tax Zones and Rates'));

        $this->_initAction()->_addBreadcrumb(__('Manage Tax Rates'), __('Manage Tax Rates'));
        $this->_view->renderLayout();
    }
}
