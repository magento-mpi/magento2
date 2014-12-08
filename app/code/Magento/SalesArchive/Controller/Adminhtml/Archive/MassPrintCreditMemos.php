<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassPrintCreditMemos extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Print Credit Memos mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('pdfcreditmemos', 'order', null, ['origin' => 'archive']);
    }
}
