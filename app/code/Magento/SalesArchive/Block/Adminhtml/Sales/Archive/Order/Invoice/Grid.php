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

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Invoice;

class Grid
    extends \Magento\Adminhtml\Block\Sales\Invoice\Grid
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
        return 'Magento\SalesArchive\Model\Resource\Order\Invoice\Collection';
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
                $url = \Mage::helper('Magento\Core\Helper\Url')->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl(\Mage::helper('Magento\Core\Helper\Url')->addRequestParam($url, array('action' => 'invoice')));
            }
            return $this->_exportTypes;
        }
        return false;
    }
}
