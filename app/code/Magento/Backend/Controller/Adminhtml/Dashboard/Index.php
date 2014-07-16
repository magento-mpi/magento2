<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Dashboard;

class Index extends \Magento\Backend\Controller\Adminhtml\Dashboard
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Dashboard'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::dashboard');
        $this->_addBreadcrumb(__('Dashboard'), __('Dashboard'));
        $this->_view->renderLayout();
    }
}
