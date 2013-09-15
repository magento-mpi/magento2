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
namespace Magento\GoogleShopping\Model;

class Item extends \Magento\Core\Model\AbstractModel
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
            $this->_serviceItem = \Mage::getModel('Magento\GoogleShopping\Model\Service\Item')
                ->setStoreId($this->getStoreId());
        }
        return $this->_serviceItem;
    }

    /**
     * Set Service Item Instance
     *
     * @param \Magento\GoogleShopping\Model\Service\Item $service
     * @return \Magento\GoogleShopping\Model\Item
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
        return \Mage::getSingleton('Magento\GoogleShopping\Model\Config')->getTargetCountry($this->getStoreId());
    }

    /**
     * Save item to Google Content
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\GoogleShopping\Model\Item
     */
    public function insertItem(\Magento\Catalog\Model\Product $product)
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
     * @return \Magento\GoogleShopping\Model\Item
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
     * @return \Magento\GoogleShopping\Model\Item
     */
    public function deleteItem()
    {
        $this->getServiceItem()->delete($this);
        return $this;
    }

    /**
     * Load Item Model by Product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\GoogleShopping\Model\Item
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

        $registry = $this->_coreRegistry->registry(self::TYPES_REGISTRY_KEY);
        if (is_array($registry) && isset($registry[$attributeSetId][$targetCountry])) {
            return $registry[$attributeSetId][$targetCountry];
        }

        $type = \Mage::getModel('Magento\GoogleShopping\Model\Type')
            ->loadByAttributeSetId($attributeSetId, $targetCountry);

        $registry[$attributeSetId][$targetCountry] = $type;
        $this->_coreRegistry->unregister(self::TYPES_REGISTRY_KEY);
        $this->_coreRegistry->register(self::TYPES_REGISTRY_KEY, $registry);

        return $type;
    }

    /**
     * Product Getter. Load product if not exist.
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (is_null($this->getData('product')) && !is_null($this->getProductId())) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')
                ->setStoreId($this->getStoreId())
                ->load($this->getProductId());
            $this->setData('product', $product);
        }

        return $this->getData('product');
    }

    /**
     * Product Setter.
     *
     * @param \Magento\Catalog\Model\Product
     * @return \Magento\GoogleShopping\Model\Item
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->setData('product', $product);
        $this->setProductId($product->getId());
        $this->setStoreId($product->getStoreId());

        return $this;
    }
}
