<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist reports controller
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Controller_Adminhtml_Report_Customer_Wishlist extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init layout and add breadcrumbs
     *
     * @return Enterprise_Wishlist_Controller_Adminhtml_Report_Customer_Wishlist
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Enterprise_Wishlist::report_customers_wishlist')
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Customers'),
                __('Customers')
            );
        return $this;
    }

    /**
     * Index Action.
     * Forward to Wishlist Action
     */
    public function indexAction()
    {
        $this->_forward('wishlist');
    }

    /**
     * Wishlist view action
     */
    public function wishlistAction()
    {
        $this->_title(__("Customer Wish List Report"));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Export Excel Action
     */
    public function exportExcelAction()
    {
        $this->loadLayout();
        $fileName = 'customer_wishlists.xml';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.customer.wishlist.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Export Csv Action
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'customer_wishlists.csv';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock  */
 	 	$exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.customer.wishlist.grid', 'grid.export');
 	 	$this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Retrieve admin session model
     *
     * @return Magento_Backend_Model_Auth_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('Magento_Backend_Model_Auth_Session');
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return  $this->_authorization->isAllowed('Enterprise_Wishlist::wishlist');
    }
}
