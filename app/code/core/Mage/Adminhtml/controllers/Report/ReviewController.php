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
 * Review reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Report_ReviewController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_addBreadcrumb(__('Reports'), __('Reports'))
            ->_addBreadcrumb(__('Review'), __('Reviews'));
        return $this;
    }

    public function customerAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/customer')
            ->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_customer'))
            ->renderLayout();
    }
    
    /**
     * Export review customer report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'review_customer.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_review_customer_grid')
            ->getCsv();
        
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export review customer report to XML format
     */
    public function exportCustomerXmlAction()
    {
        $fileName   = 'review_customer.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_review_customer_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function productAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/product')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_product'))
            ->renderLayout();
    }

    /**
     * Export review product report to CSV format
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'review_product.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_review_product_grid')
            ->getCsv();
        
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export review product report to XML format
     */
    public function exportProductXmlAction()
    {
        $fileName   = 'review_product.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_review_product_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    public function productDetailAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/productDetail')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addBreadcrumb(__('Product Reviews'), __('Product Reviews'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_detail'))
            ->renderLayout();
    }

    /**
     * Export review product detail report to CSV format
     */
    public function exportProductDetailCsvAction()
    {
        $fileName   = 'review_product_detail.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_review_detail_grid')
            ->getCsv();
        
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export review product detail report to XML format
     */
    public function exportProductDetailXmlAction()
    {
        $fileName   = 'review_product_detail.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_review_detail_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
	    switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return Mage::getSingleton('admin/session')->isAllowed('report/review/customer');
                break;
            case 'product':
                return Mage::getSingleton('admin/session')->isAllowed('report/review/product');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/review');
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
    }
}