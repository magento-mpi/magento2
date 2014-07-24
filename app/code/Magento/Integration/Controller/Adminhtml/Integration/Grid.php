<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Controller\Adminhtml\Integration;

class Grid extends \Magento\Integration\Controller\Adminhtml\Integration
{
    /**
     * AJAX integrations grid.
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
