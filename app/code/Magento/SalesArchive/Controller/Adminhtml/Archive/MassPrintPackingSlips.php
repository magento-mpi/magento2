<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
