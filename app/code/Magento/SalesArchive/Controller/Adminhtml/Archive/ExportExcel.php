<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
