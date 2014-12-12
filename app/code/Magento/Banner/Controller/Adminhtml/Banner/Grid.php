<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Grid extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Render Banner grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
