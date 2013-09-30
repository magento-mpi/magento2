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
 * Associated products collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollection
    extends Magento_Catalog_Model_Resource_Product_Link_Product_Collection
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Catalog_Model_ProductTypes_ConfigInterface
     */
    protected $_config;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Model_ProductTypes_ConfigInterface $config
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $coreResource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Eav_Model_Resource_Helper $resourceHelper,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Catalog_Model_ProductTypes_ConfigInterface $config
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
        parent::__construct(
            $eventManager,
            $logger,
            $fetchStrategy,
            $entityFactory,
            $eavConfig,
            $coreResource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $catalogData,
            $catalogProductFlat,
            $coreStoreConfig
        );
    }

    /**
     * Retrieve currently edited product model
     *
     * @return Magento_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @inheritdoc
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $configData = $this->_config->getType('grouped');
        $allowProductTypes = isset($configData['allow_product_types']) ? $configData['allow_product_types'] : array();
        $this->setProduct($this->_getProduct())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes);

        return $this;
    }
}
