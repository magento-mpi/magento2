<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * sales admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Reports'), Mage::helper('adminhtml')->__('Reports'));
        return $this;
    }

    public function salesAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales'))
            ->renderLayout();
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function couponsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/coupons')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Coupons Reports'), Mage::helper('adminhtml')->__('Coupons Reports'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_coupons'))
            ->renderLayout();
    }

    /**
     * Export coupons report grid to CSV format
     */
    public function exportCouponsCsvAction()
    {
        $fileName   = 'coupons.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_coupons_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export coupons report grid to Excel XML format
     */
    public function exportCouponsExcelAction()
    {
        $fileName   = 'coupons.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_coupons_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function wishlistAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/wishlist')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Wishlist Report'), Mage::helper('adminhtml')->__('Wishlist Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_wishlist'))
            ->renderLayout();
    }

    /**
     * Export wishlist report grid to CSV format
     */
    public function exportWishlistCsvAction()
    {
        $fileName   = 'wishlist.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_wishlist_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export wishlist report to XML format
     */
    public function exportWishlistXmlAction()
    {
        $fileName   = 'wishlist.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_wishlist_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function searchAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/search')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Search Report'), Mage::helper('adminhtml')->__('Search Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_search'))
            ->renderLayout();
    }

    /**
     * Export search report grid to CSV format
     */
    public function exportSearchCsvAction()
    {
        $fileName   = 'search.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_search_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export search report to XML format
     */
    public function exportSearchXmlAction()
    {
        $fileName   = 'search.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_search_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
/*
    public function customersAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/customers')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Best Customers'), Mage::helper('adminhtml')->__('Best Customers'))
            ->renderLayout();
    }
*/
    public function ordersAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/orders')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Recent Orders'), Mage::helper('adminhtml')->__('Recent Orders'))
            ->renderLayout();
    }

    public function totalsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/totals')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Order Totals'), Mage::helper('adminhtml')->__('Order Totals'))
            ->renderLayout();
    }

    protected function _isAllowed()
    {
	    switch ($this->getRequest()->getActionName()) {
            case 'sales':
                return Mage::getSingleton('admin/session')->isAllowed('report/sales');
                break;
            case 'products':
                return Mage::getSingleton('admin/session')->isAllowed('report/products');
                break;
            case 'coupons':
                return Mage::getSingleton('admin/session')->isAllowed('report/coupons');
                break;
            case 'wishlist':
                return Mage::getSingleton('admin/session')->isAllowed('report/wishlist');
                break;
            case 'search':
                return Mage::getSingleton('admin/session')->isAllowed('report/search');
                break;
            /*
            case 'customers':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
                break;
            */
            case 'orders':
                return Mage::getSingleton('admin/session')->isAllowed('report/orders');
                break;
            case 'totals':
                return Mage::getSingleton('admin/session')->isAllowed('report/totals');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report');
                break;
        }
    }

    protected function _sendUploadResponse($fileName, $content)
    {
        header('HTTP/1.1 200 OK');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Last-Modified: '.date('r'));
        header("Accept-Ranges: bytes");
        header("Content-Length: ".sizeof($content));
        header("Content-type: application/octet-stream");
        echo $content;
        exit;
    }
}