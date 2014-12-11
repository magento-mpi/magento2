<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassPrintPackingSlips extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Print packing slips mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('pdfshipments', 'order', null, ['origin' => 'archive']);
    }
}
