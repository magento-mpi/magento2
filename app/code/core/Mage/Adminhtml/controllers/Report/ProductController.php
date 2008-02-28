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
 * Product reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmytro Vasylenko <dmitriy@varien.com>
 */
class Mage_Adminhtml_Report_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Products'), Mage::helper('reports')->__('Products'));
        return $this;
    }

    public function orderedAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/ordered')
            ->_addBreadcrumb(Mage::helper('reports')->__('Bestsellers'), Mage::helper('reports')->__('Bestsellers'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_ordered'))
            ->renderLayout();
    }

    /**
     * Export products bestsellers report to CSV format
     */
    public function exportOrderedCsvAction()
    {
        $fileName   = 'products_bestsellers.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_ordered_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products bestsellers report to XML format
     */
    public function exportOrderedXmlAction()
    {
        $fileName   = 'products_bestsellers.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_ordered_grid')
            ->getXml($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function viewedAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/viewed')
            ->_addBreadcrumb(Mage::helper('reports')->__('Most viewed'), Mage::helper('reports')->__('Most viewed'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_viewed'))
            ->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     */
    public function exportViewedCsvAction()
    {
        $fileName   = 'products_mostviewed.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products most viewed report to XML format
     */
    public function exportViewedXmlAction()
    {
        $fileName   = 'products_mostviewed.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid')
            ->getXml($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'ordered':
                return Mage::getSingleton('admin/session')->isAllowed('report/product/ordered');
                break;
            case 'viewed':
                return Mage::getSingleton('admin/session')->isAllowed('report/product/viewed');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/product');
                break;
        }
    }
}