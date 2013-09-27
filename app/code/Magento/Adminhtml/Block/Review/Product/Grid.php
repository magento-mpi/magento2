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
 * Adminhtml product grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Review_Product_Grid extends Magento_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * @var Magento_Core_Model_Resource_Website_CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param Magento_Core_Model_Resource_Website_CollectionFactory $websitesFactory
     * @param Magento_Core_Model_WebsiteFactory $websiteFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setsFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Catalog_Model_Product_Type $type
     * @param Magento_Catalog_Model_Product_Status $status
     * @param Magento_Catalog_Model_Product_Visibility $visibility
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Resource_Website_CollectionFactory $websitesFactory,
        Magento_Core_Model_WebsiteFactory $websiteFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setsFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_Product_Type $type,
        Magento_Catalog_Model_Product_Status $status,
        Magento_Catalog_Model_Product_Visibility $visibility,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_websitesFactory = $websitesFactory;
        parent::__construct(
            $websiteFactory, $setsFactory, $productFactory, $type, $status, $visibility, $catalogData, $coreData,
            $context, $storeManager, $urlModel, $data
        );
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setRowClickCallback('review.gridRowClick');
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
                'header'    => __('ID'),
                'width'     => '50px',
                'index'     => 'entity_id',
        ));

        $this->addColumn('name', array(
                'header'    => __('Name'),
                'index'     => 'name',
        ));

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', array(
                    'header'    => __('Product Store Name'),
                    'index'     => 'custom_name'
            ));
        }

        $this->addColumn('sku', array(
                'header'    => __('SKU'),
                'width'     => '80px',
                'index'     => 'sku'
        ));

        $this->addColumn('price', array(
                'header'    => __('Price'),
                'type'      => 'currency',
                'index'     => 'price'
        ));

        $this->addColumn('qty', array(
                'header'    => __('Quantity'),
                'width'     => '130px',
                'type'      => 'number',
                'index'     => 'qty'
        ));

        $this->addColumn('status', array(
                'header'    => __('Status'),
                'width'     => '90px',
                'index'     => 'status',
                'type'      => 'options',
                'source'    => 'Magento_Catalog_Model_Product_Status',
                'options'   => $this->_status->getOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> __('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => $this->_websitesFactory->create()->toOptionHash(),
            ));
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/jsonProductInfo', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        return $this;
    }
}
