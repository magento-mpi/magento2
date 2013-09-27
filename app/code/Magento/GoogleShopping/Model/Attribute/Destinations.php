<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Control (destinations) attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Destinations extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * Config
     *
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_GoogleShopping_Model_Config $config
     * @param Magento_GoogleShopping_Helper_Data $gsData
     * @param Magento_GoogleShopping_Helper_Product $gsProduct
     * @param Magento_GoogleShopping_Helper_Price $gsPrice
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_GoogleShopping_Model_Resource_Attribute $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_GoogleShopping_Model_Config $config,
        Magento_GoogleShopping_Helper_Data $gsData,
        Magento_GoogleShopping_Helper_Product $gsProduct,
        Magento_GoogleShopping_Helper_Price $gsPrice,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_GoogleShopping_Model_Resource_Attribute $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($productFactory, $gsData, $gsProduct, $gsPrice, $context, $registry, $resource,
            $resourceCollection, $data);
    }

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $destInfo = $this->_config->getDestinationsInfo($product->getStoreId());
        $entry->setDestinationsMode($destInfo);

        return $entry;
    }
}
