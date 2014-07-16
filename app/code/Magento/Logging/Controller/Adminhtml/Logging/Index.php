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
        $this->_title->add(__('Report'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->_view->renderLayout();
    }
}
