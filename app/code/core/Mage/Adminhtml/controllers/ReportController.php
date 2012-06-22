<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * sales admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reports'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reports'));
        return $this;
    }


/*
    public function wishlistAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/wishlist')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Wishlist Report'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Wishlist Report'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Wishlist'))
            ->renderLayout();
    }

    /**
     * Export wishlist report grid to CSV format
     * /
    public function exportWishlistCsvAction()
    {
        $fileName   = 'wishlist.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Wishlist_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export wishlist report to Excel XML format
     * /
    public function exportWishlistExcelAction()
    {
        $fileName   = 'wishlist.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Wishlist_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
*/
    public function searchAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Search Terms'));

        Mage::dispatchEvent('on_view_report', array('report' => 'search'));

        $this->_initAction()
            ->_setActiveMenu('report/search')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Search Terms'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Search Terms'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Search'))
            ->renderLayout();
    }

    /**
     * Export search report grid to CSV format
     */
    public function exportSearchCsvAction()
    {
        $fileName   = 'search.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Search_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export search report to Excel XML format
     */
    public function exportSearchExcelAction()
    {
        $fileName   = 'search.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Search_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
/*
    public function ordersAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/orders')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Recent Orders'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Recent Orders'))
            ->renderLayout();
    }

    public function totalsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/totals')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Order Totals'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Order Totals'))
            ->renderLayout();
    }
*/

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'search':
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/search');
                break;
            /*
            case 'customers':
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/shopcart');
                break;
            */
            default:
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report');
                break;
        }
    }
}
