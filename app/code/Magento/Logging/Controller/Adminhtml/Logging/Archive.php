<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
