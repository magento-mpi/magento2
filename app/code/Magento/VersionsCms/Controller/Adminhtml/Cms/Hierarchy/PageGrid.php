<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
