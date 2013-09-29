<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftRegistry entity item collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Resource\Item;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * List of product IDs
     * Contains IDs of products related to items and their options
     *
     * @var array
     */
    protected $_productIds = array();

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Item', 'Magento\GiftRegistry\Model\Resource\Item');
    }

    /**
     * Add gift registry filter to collection
     *
     * @param int $entityId
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()
            ->join(array('e' => $this->getTable('magento_giftregistry_entity')),
                'e.entity_id = main_table.entity_id', 'website_id')
            ->where('main_table.entity_id = ?', (int)$entityId);

        return $this;
    }

    /**
     * Add product filter to collection
     *
     * @param int $productId
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function addProductFilter($productId)
    {
        if ((int)$productId > 0) {
            $this->addFieldToFilter('product_id', (int)$productId);
        }

        return $this;
    }

    /**
     * Add item filter to collection
     *
     * @param int|array $itemId
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function addItemFilter($itemId)
    {
        if (is_array($itemId)) {
            $this->addFieldToFilter('item_id', array('in' => $itemId));
        } elseif ((int)$itemId > 0) {
            $this->addFieldToFilter('item_id', (int)$itemId);
        }

        return $this;
    }

    /**
     * After load processing
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        // Assign options and products
        $this->_assignOptions();
        $this->_assignProducts();
        $this->resetItemsDataChanged();

        return $this;
    }

    /**
     * Assign options to items
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    protected function _assignOptions()
    {
        $itemIds = array_keys($this->_items);
        $optionCollection = \Mage::getModel('Magento\GiftRegistry\Model\Item\Option')->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }
        $productIds = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Assign products to items and their options
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    protected function _assignProducts()
    {
        $productIds = array();
        foreach ($this as $item) {
            $productIds[] = $item->getProductId();
        }
        $this->_productIds = array_merge($this->_productIds, $productIds);

        $productCollection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
            ->setStoreId(\Mage::app()->getStore()->getId())
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect(\Mage::getSingleton('Magento\Sales\Model\Quote\Config')->getProductAttributes())
            ->addStoreFilter()
            ->addUrlRewrite()
            ->addOptionsToResult();

        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                $product->setCustomOptions(array());
                foreach ($item->getOptions() as $option) {
                    $option->setProduct($productCollection->getItemById($option->getProductId()));
                }
                $item->setProduct($product);
                $item->setProductName($product->getName());
                $item->setProductSku($product->getSku());
                $item->setProductPrice($product->getPrice());
            } else {
                $item->isDeleted(true);
            }
        }
        return $this;
    }

    /**
     * Update items custom price (Depends on custom options)
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function updateItemAttributes()
    {
        foreach ($this->getItems() as $item) {
            $product = $item->getProduct();
            $product->setSkipCheckRequiredOption(true);
            $product->getStore()->setWebsiteId($item->getWebsiteId());
            $product->setCustomOptions($item->getOptionsByCode());
            $item->setPrice($product->getFinalPrice());
            $simpleOption = $product->getCustomOption('simple_product');
            if ($simpleOption) {
                $item->setSku($simpleOption->getProduct()->getSku());
            } else {
                $item->setSku($product->getSku());
            }
        }
        return $this;
    }
}
