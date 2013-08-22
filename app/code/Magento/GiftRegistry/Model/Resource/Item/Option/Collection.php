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
 * Gift registry item option collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Resource_Item_Option_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * List of option ids grouped by item id
     *
     * @var array
     */
    protected $_optionsByItem = array();

    /**
     * List of option ids grouped by product id
     *
     * @var array
     */
    protected $_optionsByProduct = array();

    /**
     * Internal constructor
     * Defines resource model for collection
     */
    protected function _construct()
    {
        $this->_init('Magento_GiftRegistry_Model_Item_Option', 'Magento_GiftRegistry_Model_Resource_Item_Option');
    }

    /**
     * After load processing
     * Fills the lists of options grouped by item and product
     *
     * @return Magento_GiftRegistry_Model_Resource_Item_Option_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this as $option) {
            $optionId = $option->getId();
            $itemId = $option->getItemId();
            $productId = $option->getProductId();
            if (isset($this->_optionsByItem[$itemId])) {
                $this->_optionsByItem[$itemId][] = $optionId;
            } else {
                $this->_optionsByItem[$itemId] = array($optionId);
            }
            if (isset($this->_optionsByProduct[$productId])) {
                $this->_optionsByProduct[$productId][] = $optionId;
            } else {
                $this->_optionsByProduct[$productId] = array($optionId);
            }
        }

        return $this;
    }

    /**
     * Apply gift registry item(s) filter to collection
     *
     * @param  int|array $item
     * @return Magento_GiftRegistry_Model_Resource_Item_Option_Collection
     */
    public function addItemFilter($item)
    {
        if (empty($item)) {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        } else if (is_array($item)) {
            $this->addFieldToFilter('item_id', array('in' => $item));
        } else if ($item instanceof Magento_GiftRegistry_Model_Item) {
            $this->addFieldToFilter('item_id', $item->getId());
        } else {
            $this->addFieldToFilter('item_id', $item);
        }

        return $this;
    }

    /**
     * Apply product(s) filter to collection
     *
     * @param  int|Magento_Catalog_Model_Product|array $product
     * @return Magento_GiftRegistry_Model_Resource_Item_Option_Collection
     */
    public function addProductFilter($product)
    {
        if (is_array($product)) {
            $this->addFieldToFilter('product_id', array('in' => $product));
        } else if ($product instanceof Magento_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } elseif ((int)$product > 0) {
            $this->addFieldToFilter('product_id', (int)$product);
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }

        return $this;
    }

    /**
     * Retrieve the list of all product IDs
     *
     * @return array
     */
    public function getProductIds()
    {
        $this->load();

        return array_keys($this->_optionsByProduct);
    }

    /**
     * Retrieve all options related to the specified gift registry item
     *
     * @param  mixed $item
     * @return array
     */
    public function getOptionsByItem($item)
    {
        if ($item instanceof Magento_GiftRegistry_Model_Item) {
            $itemId = $item->getId();
        } else {
            $itemId = $item;
        }

        $this->load();

        $options = array();
        if (isset($this->_optionsByItem[$itemId])) {
            foreach ($this->_optionsByItem[$itemId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }

    /**
     * Retrieve all options related to the specified product
     *
     * @param  mixed $product
     * @return array
     */
    public function getOptionsByProduct($product)
    {
        if ($product instanceof Magento_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $this->load();

        $options = array();
        if (isset($this->_optionsByProduct[$productId])) {
            foreach ($this->_optionsByProduct[$productId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }
}
