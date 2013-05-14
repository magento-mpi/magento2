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
class Enterprise_Wishlist_Adminhtml_Report_Customer_WishlistController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout and add breadcrumbs
     *
     * @return Enterprise_Wishlist_Adminhtml_Report_Customer_WishlistController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_Reports::report_customers')
            ->_addBreadcrumb(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Reports'),
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Reports')
            )
            ->_addBreadcrumb(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Customers'),
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Customers')
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
        $this->_title($this->__("Customer's wishlists"));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Export Excel Action
     */
    public function exportExcelAction()
    {
        $fileName = 'customer_wishlists.xml';
        $content = $this->getLayout()
            ->createBlock('Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist_Grid')
            ->getExcelFile($fileName);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Csv Action
     */
    public function exportCsvAction()
    {
        $fileName = 'customer_wishlists.csv';
        $content = $this->getLayout()
            ->createBlock('Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist_Grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Retrieve admin session model
     *
     * @return Mage_Backend_Model_Auth_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session');
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
