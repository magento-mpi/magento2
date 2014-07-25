<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class ExportExcel extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     *  Export order grid to Excel XML format
     *
     * @return void
     */
    public function execute()
    {
        $this->_export('xml');
    }
}
