<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in compared list
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Compared
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'compared';

    /**
     * @var Magento_Adminhtml_Helper_Sales
     */
    protected $_adminhtmlSales;

    /**
     * @var Magento_Catalog_Model_Product_Compare_ListFactory|null
     */
    protected $_compareListFactory;

    /**
     * @var Magento_Catalog_Model_Config
     */
    protected $_catalogConfig;

    /**
     * @var Magento_CatalogInventory_Model_Stock_Status
     */
    protected $_catalogStockStatus;

    /**
     * @param Magento_CatalogInventory_Model_Stock_Status $catalogStockStatus
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Data_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Model_Product_Compare_ListFactory $compareListFactory
     * @param array $data
     */
    public function __construct(
        Magento_CatalogInventory_Model_Stock_Status $catalogStockStatus,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Data_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Catalog_Model_Product_Compare_ListFactory $compareListFactory,
        array $data = array()
    ) {
        $this->_catalogStockStatus = $catalogStockStatus;
        $this->_catalogConfig = $catalogConfig;
        parent::__construct($collectionFactory, $coreData, $context, $storeManager, $urlModel, $coreRegistry, $data);
        $this->_adminhtmlSales = $adminhtmlSales;
        $this->_compareListFactory = $compareListFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_compared');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Products in the Comparison List (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Return items collection
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = $this->_catalogConfig->getProductAttributes();
            $collection = $this->_compareListFactory->create()
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId())
                ->addAttributeToSelect($attributes)
                ->addAttributeToFilter('status', Magento_Catalog_Model_Product_Status::STATUS_ENABLED);
            $this->_catalogStockStatus->addIsInStockFilterToCollection($collection);
            $collection = $this->_adminhtmlSales->applySalableProductTypesFilter($collection);
            $collection->addOptionsToResult();
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewCompared', array('_current'=>true));
    }
}
