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
 * Catalog compare item resource model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Catalog_Model_Resource_Product_Collection_AssociatedProduct
    extends Magento_Catalog_Model_Resource_Product_Collection
{
    /**
     * Registry instance
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * Product type configurable instance
     *
     * @var Magento_Catalog_Model_Product_Type_Configurable
     */
    protected $_productType;

    /**
     * Configuration helper instance
     *
     * @var Magento_Catalog_Helper_Product_Configuration
     */
    protected $_configurationHelper;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Catalog_Model_Product_OptionFactory $productOptionFactory
     * @param Magento_Catalog_Model_Resource_Url $catalogUrl
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Model_Resource_Helper $resourceHelper
     * @param Magento_Core_Model_Registry $registryManager
     * @param Magento_Catalog_Model_Product_Type_Configurable $productType
     * @param Magento_Catalog_Helper_Product_Configuration $configurationHelper
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $coreResource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Catalog_Model_Product_OptionFactory $productOptionFactory,
        Magento_Catalog_Model_Resource_Url $catalogUrl,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Model_Resource_Helper $resourceHelper,
        Magento_Core_Model_Registry $registryManager,
        Magento_Catalog_Model_Product_Type_Configurable $productType,
        Magento_Catalog_Helper_Product_Configuration $configurationHelper
    ) {
        $this->_registryManager = $registryManager;
        $this->_productType = $productType;
        $this->_configurationHelper = $configurationHelper;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $eavConfig, $coreResource,
            $eavEntityFactory, $universalFactory, $storeManager, $catalogData, $catalogProductFlat, $coreStoreConfig,
            $productOptionFactory, $catalogUrl, $locale, $customerSession, $resourceHelper
        );
    }

    /**
     * Get product type
     *
     * @return Magento_Catalog_Model_Product_Type_Configurable
     */
    public function getProductType()
    {
        return $this->_productType;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return mixed
     */
    private function getProduct()
    {
        return $this->_registryManager->registry('current_product');
    }

    /**
     * Add attributes to select
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $allowedProductTypes = $this->_configurationHelper->getConfigurableAllowedTypes();

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('weight')
            ->addAttributeToSelect('image')
            ->addFieldToFilter('type_id', $allowedProductTypes)
            ->addFieldToFilter('entity_id', array('neq' => $this->getProduct()->getId()))
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');

        return $this;
    }
}
