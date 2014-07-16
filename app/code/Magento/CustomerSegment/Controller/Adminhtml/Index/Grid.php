<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Grid extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Grid ajax action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
