<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
