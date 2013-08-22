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
 * Archive shipments grid block
 *
 */

class Magento_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Shipment_Grid
    extends Magento_Adminhtml_Block_Sales_Shipment_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('sales_shipment_grid_archive');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento_SalesArchive_Model_Resource_Order_Shipment_Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('*/*/shipmentsgrid', array('_current' => true));
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
                $exportType->setUrl(Mage::helper('Magento_Core_Helper_Url')
                    ->addRequestParam($url, array('action' => 'shipment')));
            }
            return $this->_exportTypes;
        }
        return false;
    }

    /**
     * Prepare and set options for massaction
     *
     * @return Magento_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Shipment_Grid
     */
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $this->getMassactionBlock()->getItem('print_shipping_label')
            ->setUrl($this->getUrl('*/sales_archive/massPrintShippingLabel'));

        return $this;
    }
}
