<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class OrdersGrid extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Orders grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_renderGrid();
    }
}
