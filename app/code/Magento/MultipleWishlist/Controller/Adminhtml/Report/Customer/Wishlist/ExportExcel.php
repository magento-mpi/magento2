<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportExcel extends \Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist
{
    /**
     * Export Excel Action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'customer_wishlists.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.customer.wishlist.grid',
            'grid.export'
        );
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            DirectoryList::VAR_DIR
        );
    }
}
