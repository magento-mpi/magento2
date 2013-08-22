<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping Cart reports admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Report_Shopcart extends Magento_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        $this->loadLayout()
            ->_addBreadcrumb(__('Reports'), __('Reports'))
            ->_addBreadcrumb(__('Shopping Cart'), __('Shopping Cart'));
        return $this;
    }

    public function customerAction()
    {
        $this->_title(__('Customer Shopping Carts'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_shopcart_customer')
            ->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Customer'))
            ->renderLayout();
    }

    /**
     * Export shopcart customer report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'shopcart_customer.csv';
        $content    = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Customer_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export shopcart customer report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $fileName   = 'shopcart_customer.xml';
        $content    = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Customer_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productAction()
    {
        $this->_title(__('Products in Carts'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_shopcart_product')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Product'))
            ->renderLayout();
    }

    /**
     * Export products report grid to CSV format
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'shopcart_product.csv';
        $content    = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Product_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $fileName   = 'shopcart_product.xml';
        $content    = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Product_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function abandonedAction()
    {
        $this->_title(__('Abandoned Carts'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_shopcart_abandoned')
            ->_addBreadcrumb(__('Abandoned Carts'), __('Abandoned Carts'))
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Abandoned'))
            ->renderLayout();
    }

    /**
     * Export abandoned carts report grid to CSV format
     */
    public function exportAbandonedCsvAction()
    {
        $fileName   = 'shopcart_abandoned.csv';
        $content    = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Abandoned_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export abandoned carts report to Excel XML format
     */
    public function exportAbandonedExcelAction()
    {
        $fileName   = 'shopcart_abandoned.xml';
        $content    = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Shopcart_Abandoned_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return $this->_authorization->isAllowed(null);
                break;
            case 'product':
                return $this->_authorization->isAllowed('Magento_Reports::product');
                break;
            case 'abandoned':
                return $this->_authorization->isAllowed('Magento_Reports::abandoned');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::shopcart');
                break;
        }
    }
}
