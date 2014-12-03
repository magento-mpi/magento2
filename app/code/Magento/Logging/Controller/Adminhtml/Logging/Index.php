<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

class Index extends \Magento\Logging\Controller\Adminhtml\Logging
{
    /**
     * Log page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Report'));
        $this->_view->renderLayout();
    }
}
