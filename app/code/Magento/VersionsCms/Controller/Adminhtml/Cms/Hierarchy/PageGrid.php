<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy;

class PageGrid extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy
{
    /**
     * Cms Pages Ajax Grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
