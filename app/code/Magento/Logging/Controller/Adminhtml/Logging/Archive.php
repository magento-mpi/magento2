<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

class Archive extends \Magento\Logging\Controller\Adminhtml\Logging
{
    /**
     * Archive page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_backups');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Admin Actions Archive'));
        $this->_view->renderLayout();
    }
}
