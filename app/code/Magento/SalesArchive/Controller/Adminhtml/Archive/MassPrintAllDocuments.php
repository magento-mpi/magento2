<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassPrintAllDocuments extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Print all documents mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('pdfdocs', 'order', null, ['origin' => 'archive']);
    }
}
