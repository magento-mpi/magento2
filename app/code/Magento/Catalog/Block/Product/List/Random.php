<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product random items block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */

class Magento_Catalog_Block_Product_List_Random extends Magento_Catalog_Block_Product_List
{
    /**
     * Product collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Layer factory
     *
     * @var Magento_Catalog_Model_LayerFactory
     */
    protected $_layerFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_LayerFactory $layerFactory
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_LayerFactory $layerFactory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_Layer $catalogLayer,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_layerFactory = $layerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($categoryFactory, $storeManager, $catalogConfig, $catalogLayer, $coreRegistry, $taxData,
            $catalogData, $coreData, $context, $data);
    }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            /** @var Magento_Catalog_Model_Resource_Product_Collection $collection */
            $collection = $this->_productCollectionFactory->create();
            $this->_layerFactory->create()->prepareProductCollection($collection);
            $collection->getSelect()->order('rand()');
            $collection->addStoreFilter();
            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 0;
            $collection->setPage(1, $numProducts);

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
