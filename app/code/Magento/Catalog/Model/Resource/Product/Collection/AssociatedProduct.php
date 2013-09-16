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
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Registry $registryManager
     * @param Magento_Catalog_Model_Product_Type_Configurable $productType
     * @param Magento_Catalog_Helper_Product_Configuration $configurationHelper
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @internal param $Mage nto_Catalog_Helper_Product_Configuration $configurationHelper* nto_Catalog_Helper_Product_Configuration $configurationHelper
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Registry $registryManager,
        Magento_Catalog_Model_Product_Type_Configurable $productType,
        Magento_Catalog_Helper_Product_Configuration $configurationHelper,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_registryManager = $registryManager;
        $this->_productType = $productType;
        $this->_configurationHelper = $configurationHelper;
        parent::__construct($catalogData, $catalogProductFlat, $eventManager, $fetchStrategy, $coreStoreConfig);
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

        $allowedProductTypes = array();
        foreach ($this->_configurationHelper->getConfigurableAllowedTypes() as $type) {
            $allowedProductTypes[] = $type->getName();
        }

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
