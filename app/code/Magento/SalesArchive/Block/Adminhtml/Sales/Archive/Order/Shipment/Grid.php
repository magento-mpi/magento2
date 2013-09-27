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
    /**
     * Core url
     *
     * @var Magento_Core_Helper_Url
     */
    protected $_coreUrl = null;

    /**
     * @param Magento_Sales_Model_Resource_Order_Collection_Factory $collectionFactory
     * @param Magento_Core_Helper_Url $coreUrl
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Model_Resource_Order_Collection_Factory $collectionFactory,
        Magento_Core_Helper_Url $coreUrl,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        parent::__construct($collectionFactory, $coreData, $context, $storeManager, $urlModel, $data);
    }

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
                $url = $this->_coreUrl->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl($this->_coreUrl
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
