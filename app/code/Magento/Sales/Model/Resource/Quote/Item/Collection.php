<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote item resource collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Item_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection quote instance
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Product Ids array
     *
     * @var array
     */
    protected $_productIds   = array();

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Quote_Item', 'Magento_Sales_Model_Resource_Quote_Item');
    }

    /**
     * Retrieve store Id (From Quote)
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->_quote->getStoreId();
    }

    /**
     * Set Quote object to Collection
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return Magento_Sales_Model_Resource_Quote_Item_Collection
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        $quoteId      = $quote->getId();
        if ($quoteId) {
            $this->addFieldToFilter('quote_id', $quote->getId());
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Reset the collection and inner join it to quotes table
     * Optionally can select items with specified product id only
     *
     * @param string $quotesTableName
     * @param int $productId
     * @return Magento_Sales_Model_Resource_Quote_Item_Collection
     */
    public function resetJoinQuotes($quotesTableName, $productId = null)
    {
        $this->getSelect()->reset()
            ->from(
                array('qi' => $this->getResource()->getMainTable()),
                array('item_id', 'qty', 'quote_id'))
            ->joinInner(
                array('q' => $quotesTableName),
               'qi.quote_id = q.entity_id',
                array('store_id', 'items_qty', 'items_count')
            );
        if ($productId) {
            $this->getSelect()->where('qi.product_id = ?', (int)$productId);
        }
        return $this;
    }

    /**
     * After load processing
     *
     * @return Magento_Sales_Model_Resource_Quote_Item_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        /**
         * Assign parent items
         */
        foreach ($this as $item) {
            if ($item->getParentItemId()) {
                $item->setParentItem($this->getItemById($item->getParentItemId()));
            }
            if ($this->_quote) {
                $item->setQuote($this->_quote);
            }
        }

        /**
         * Assign options and products
         */
        $this->_assignOptions();
        $this->_assignProducts();
        $this->resetItemsDataChanged();

        return $this;
    }

    /**
     * Add options to items
     *
     * @return Magento_Sales_Model_Resource_Quote_Item_Collection
     */
    protected function _assignOptions()
    {
        $itemIds          = array_keys($this->_items);
        $optionCollection = Mage::getModel('Magento_Sales_Model_Quote_Item_Option')->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }
        $productIds        = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Add products to items and item options
     *
     * @return Magento_Sales_Model_Resource_Quote_Item_Collection
     */
    protected function _assignProducts()
    {
        Magento_Profiler::start('QUOTE:'.__METHOD__, array('group' => 'QUOTE', 'method' => __METHOD__));
        $productIds = array();
        foreach ($this as $item) {
            $productIds[] = (int)$item->getProductId();
        }
        $this->_productIds = array_merge($this->_productIds, $productIds);

        $productCollection = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
            ->setStoreId($this->getStoreId())
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect(Mage::getSingleton('Magento_Sales_Model_Quote_Config')->getProductAttributes())
            ->addOptionsToResult()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->addTierPriceData();

        Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
            'collection'            => $productCollection,
            'store_id'              => $this->getStoreId(),
        ));
        Mage::dispatchEvent('sales_quote_item_collection_products_after_load', array(
            'product_collection'    => $productCollection
        ));

        $recollectQuote = false;
        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                $product->setCustomOptions(array());
                $qtyOptions         = array();
                $optionProductIds   = array();
                foreach ($item->getOptions() as $option) {
                    /**
                     * Call type-specific logic for product associated with quote item
                     */
                    $product->getTypeInstance()->assignProductToOption(
                            $productCollection->getItemById($option->getProductId()),
                            $option,
                            $product
                        );

                    if (is_object($option->getProduct()) && $option->getProduct()->getId() != $product->getId()) {
                        $optionProductIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
                    }
                }

                if ($optionProductIds) {
                    foreach ($optionProductIds as $optionProductId) {
                        $qtyOption = $item->getOptionByCode('product_qty_' . $optionProductId);
                        if ($qtyOption) {
                            $qtyOptions[$optionProductId] = $qtyOption;
                        }
                    }
                }

                $item->setQtyOptions($qtyOptions)->setProduct($product);
            } else {
                $item->isDeleted(true);
                $recollectQuote = true;
            }
            $item->checkData();
        }

        if ($recollectQuote && $this->_quote) {
            $this->_quote->collectTotals();
        }
        Magento_Profiler::stop('QUOTE:'.__METHOD__);

        return $this;
    }
}

