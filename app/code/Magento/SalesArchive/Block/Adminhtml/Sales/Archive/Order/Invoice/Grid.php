<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive invoices grid block
 *
 */

class Magento_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Invoice_Grid
    extends Magento_Adminhtml_Block_Sales_Invoice_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_invoice_grid_archive');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento_SalesArchive_Model_Resource_Order_Invoice_Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('*/*/invoicesgrid', array('_current' => true));
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        if (!empty($this->_exportTypes)) {
            foreach ($this->_exportTypes as $exportType) {
                $url = Mage::helper('Magento_Core_Helper_Url')->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl(Mage::helper('Magento_Core_Helper_Url')->addRequestParam($url, array('action' => 'invoice')));
            }
            return $this->_exportTypes;
        }
        return false;
    }
}
