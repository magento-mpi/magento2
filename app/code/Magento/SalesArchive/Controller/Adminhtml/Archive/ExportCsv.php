<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class ExportCsv extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Export order grid to CSV format
     *
     * @return void
     */
    public function execute()
    {
        $this->_export('csv');
    }
}
