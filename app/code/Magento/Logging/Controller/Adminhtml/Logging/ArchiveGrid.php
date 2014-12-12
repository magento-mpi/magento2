<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

class ArchiveGrid extends \Magento\Logging\Controller\Adminhtml\Logging
{
    /**
     * Archive grid ajax action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
