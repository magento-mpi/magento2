<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model;

use Magento\Catalog\Model\Product as CatalogModelProduct;

/**
 * Google Content Item Types Model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Model\AbstractModel
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
     * @var \Magento\GoogleShopping\Model\Service\Item
     */
    protected $_serviceItem = null;

    /**
     * Config
     *
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * Item factory
     *
     * @var \Magento\GoogleShopping\Model\Service\ItemFactory
     */
    protected $_itemFactory;

    /**
     * Type factory
     *
     * @var \Magento\GoogleShopping\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\GoogleShopping\Model\Service\ItemFactory $itemFactory
     * @param \Magento\GoogleShopping\Model\TypeFactory $typeFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Model\Resource\Item $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param \Magento\GoogleShopping\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\GoogleShopping\Model\Service\ItemFactory $itemFactory,
        \Magento\GoogleShopping\Model\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GoogleShopping\Model\Resource\Item $resource,
        \Magento\Data\Collection\Db $resourceCollection,
        \Magento\GoogleShopping\Model\Config $config,
        array $data = array()
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_typeFactory = $typeFactory;
        $this->_productFactory = $productFactory;
        $this->_config = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\GoogleShopping\Model\Resource\Item');
    }

    /**
     * Return Service Item Instance
     *
     * @return \Magento\GoogleShopping\Model\Service\Item
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
     * @param \Magento\GoogleShopping\Model\Service\Item $service
     * @return $this
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
     * @param CatalogModelProduct $product
     * @return $this
     */
    public function insertItem(CatalogModelProduct $product)
    {
        $this->setProduct($product);
        $this->getServiceItem()->insert($this);
        $this->setTypeId($this->getType()->getTypeId());

        return $this;
    }

    /**
     * Update Item data
     *
     * @return $this
     */
    public function updateItem()
    {
        if ($this->getId()) {
            $this->getServiceItem()->update($this);
        }
        return $this;
    }

    /**
     * Delete Item from Google Content
     *
     * @return $this
     */
    public function deleteItem()
    {
        $this->getServiceItem()->delete($this);
        return $this;
    }

    /**
     * Load Item Model by Product
     *
     * @param CatalogModelProduct $product
     * @return $this
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
     * @return \Magento\GoogleShopping\Model\Type
     */
    public function getType()
    {
        $attributeSetId = $this->getProduct()->getAttributeSetId();
        $targetCountry = $this->getTargetCountry();

        $registry = $this->_registry->registry(self::TYPES_REGISTRY_KEY);
        if (is_array($registry) && isset($registry[$attributeSetId][$targetCountry])) {
            return $registry[$attributeSetId][$targetCountry];
        }

        $type = $this->_typeFactory->create()->loadByAttributeSetId($attributeSetId, $targetCountry);

        $registry[$attributeSetId][$targetCountry] = $type;
        $this->_registry->unregister(self::TYPES_REGISTRY_KEY);
        $this->_registry->register(self::TYPES_REGISTRY_KEY, $registry);

        return $type;
    }

    /**
     * Product Getter. Load product if not exist.
     *
     * @return CatalogModelProduct
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
     * @param CatalogModelProduct $product
     * @return $this
     */
    public function setProduct(CatalogModelProduct $product)
    {
        $this->setData('product', $product);
        $this->setProductId($product->getId());
        $this->setStoreId($product->getStoreId());

        return $this;
    }
}
