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
 * Google Content Item Types Model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Item extends Magento_Core_Model_Abstract
{
    /**
     * Registry keys for caching attributes and types
     *
     * @var string
     */
    const TYPES_REGISTRY_KEY = 'gcontent_types_registry';

    /**
     * Service Item Instance
     *
     * @var Magento_GoogleShopping_Model_Service_Item
     */
    protected $_serviceItem = null;

    /**
     * Config
     *
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * Item factory
     *
     * @var Magento_GoogleShopping_Model_Service_ItemFactory
     */
    protected $_itemFactory;

    /**
     * Type factory
     *
     * @var Magento_GoogleShopping_Model_TypeFactory
     */
    protected $_typeFactory;

    /**
     * Product factory
     *
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_GoogleShopping_Model_Service_ItemFactory $itemFactory
     * @param Magento_GoogleShopping_Model_TypeFactory $typeFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param Magento_GoogleShopping_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Magento_GoogleShopping_Model_Service_ItemFactory $itemFactory,
        Magento_GoogleShopping_Model_TypeFactory $typeFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        Magento_GoogleShopping_Model_Config $config,
        array $data = array()
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_typeFactory = $typeFactory;
        $this->_productFactory = $productFactory;
        $this->_config = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_GoogleShopping_Model_Resource_Item');
    }

    /**
     * Return Service Item Instance
     *
     * @return Magento_GoogleShopping_Model_Service_Item
     */
    public function getServiceItem()
    {
        if (is_null($this->_serviceItem)) {
            $this->_serviceItem = $this->_itemFactory->create()->setStoreId($this->getStoreId());
        }
        return $this->_serviceItem;
    }

    /**
     * Set Service Item Instance
     *
     * @param Magento_GoogleShopping_Model_Service_Item $service
     * @return Magento_GoogleShopping_Model_Item
     */
    public function setServiceItem($service)
    {
        $this->_serviceItem = $service;
        return $this;
    }

    /**
     * Target Country
     *
     * @return string Two-letters country ISO code
     */
    public function getTargetCountry()
    {
        return $this->_config->getTargetCountry($this->getStoreId());
    }

    /**
     * Save item to Google Content
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_GoogleShopping_Model_Item
     */
    public function insertItem(Magento_Catalog_Model_Product $product)
    {
        $this->setProduct($product);
        $this->getServiceItem()
            ->insert($this);
        $this->setTypeId($this->getType()->getTypeId());

        return $this;
    }

    /**
     * Update Item data
     *
     * @return Magento_GoogleShopping_Model_Item
     */
    public function updateItem()
    {
        if ($this->getId()) {
            $this->getServiceItem()
                ->update($this);
        }
        return $this;
    }

    /**
     * Delete Item from Google Content
     *
     * @return Magento_GoogleShopping_Model_Item
     */
    public function deleteItem()
    {
        $this->getServiceItem()->delete($this);
        return $this;
    }

    /**
     * Load Item Model by Product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_GoogleShopping_Model_Item
     */
    public function loadByProduct($product)
    {
        $this->setProduct($product);
        $this->getResource()->loadByProduct($this);
        return $this;
    }

    /**
     * Return Google Content Item Type Model for current Item
     *
     * @return Magento_GoogleShopping_Model_Type
     */
    public function getType()
    {
        $attributeSetId = $this->getProduct()->getAttributeSetId();
        $targetCountry = $this->getTargetCountry();

        $registry = $this->_coreRegistry->registry(self::TYPES_REGISTRY_KEY);
        if (is_array($registry) && isset($registry[$attributeSetId][$targetCountry])) {
            return $registry[$attributeSetId][$targetCountry];
        }

        $type = $this->_typeFactory->create()->loadByAttributeSetId($attributeSetId, $targetCountry);

        $registry[$attributeSetId][$targetCountry] = $type;
        $this->_coreRegistry->unregister(self::TYPES_REGISTRY_KEY);
        $this->_coreRegistry->register(self::TYPES_REGISTRY_KEY, $registry);

        return $type;
    }

    /**
     * Product Getter. Load product if not exist.
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (is_null($this->getData('product')) && !is_null($this->getProductId())) {
            $product = $this->_productFactory->create()->setStoreId($this->getStoreId())->load($this->getProductId());
            $this->setData('product', $product);
        }

        return $this->getData('product');
    }

    /**
     * Product Setter.
     *
     * @param Magento_Catalog_Model_Product
     * @return Magento_GoogleShopping_Model_Item
     */
    public function setProduct(Magento_Catalog_Model_Product $product)
    {
        $this->setData('product', $product);
        $this->setProductId($product->getId());
        $this->setStoreId($product->getStoreId());

        return $this;
    }
}
