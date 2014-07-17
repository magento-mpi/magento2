<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class ShipmentsGrid extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Shipments grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_renderGrid();
    }
}
