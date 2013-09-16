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
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Catalog_Model_ProductTypes_ConfigInterface $config
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
        parent::__construct($catalogData, $catalogProductFlat, $eventManager, $fetchStrategy);
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
