<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pci\Controller\Adminhtml\Locks;

class Grid extends \Magento\Pci\Controller\Adminhtml\Locks
{
    /**
     * Render AJAX-grid only
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
