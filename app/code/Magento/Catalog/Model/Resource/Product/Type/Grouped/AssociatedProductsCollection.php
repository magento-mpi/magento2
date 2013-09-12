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
     * @param agento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     */
    public function __construct(
        agento_Core_Model_Registry $coreRegistry,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
    ) {
        $this->_coreRegistry = $coreRegistry;
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

        $allowProductTypes = array();
        $allowProductTypeNodes = Mage::getConfig()
            ->getNode(Magento_Catalog_Model_Config::XML_PATH_GROUPED_ALLOWED_PRODUCT_TYPES)->children();
        foreach ($allowProductTypeNodes as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $this->setProduct($this->_getProduct())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes);

        return $this;
    }
}
