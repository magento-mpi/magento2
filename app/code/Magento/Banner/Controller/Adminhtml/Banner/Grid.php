<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
