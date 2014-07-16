<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist;

class ExportCsv extends \Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist
{
    /**
     * Export Csv Action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'customer_wishlists.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.block.report.customer.wishlist.grid',
            'grid.export'
        );
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
