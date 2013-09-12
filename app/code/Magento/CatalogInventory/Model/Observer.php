<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog inventory module observer
 */
class Magento_CatalogInventory_Model_Observer
{
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    protected $_checkedQuoteItems = array();

    protected $_itemsForReindex = array();

    /**
     * Array, indexed by product's id to contain stockItems of already loaded products
     * Some kind of singleton for product's stock item
     *
     * @var array
     */
    protected $_stockItemsArray = array();

    /**
     * Stock item factory
     *
     * @var Magento_CatalogInventory_Model_Stock_ItemFactory
     */
    protected $_stockItemFactory;

    /**
     * Stock model factory
     *
     * @var Magento_CatalogInventory_Model_StockFactory
     */
    protected $_stockFactory;

    /**
     * Stock status factory
     *
     * @var Magento_CatalogInventory_Model_Stock_StatusFactory
     */
    protected $_stockStatusFactory;

    /**
     * Construct
     *
     * @param Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory
     * @param Magento_CatalogInventory_Model_StockFactory $stockFactory
     * @param Magento_CatalogInventory_Model_Stock_StatusFactory $stockStatusFactory
     */
    public function __construct(
        Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory,
        Magento_CatalogInventory_Model_StockFactory $stockFactory,
        Magento_CatalogInventory_Model_Stock_StatusFactory $stockStatusFactory
    ) {
        $this->_stockItemFactory = $stockItemFactory;
        $this->_stockFactory = $stockFactory;
        $this->_stockStatusFactory = $stockStatusFactory;
    }

    /**
     * Add stock information to product
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function addInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Magento_Catalog_Model_Product) {
            $productId = intval($product->getId());
            if (!isset($this->_stockItemsArray[$productId])) {
                $this->_stockItemsArray[$productId] = $this->_stockItemFactory->create();
            }
            $productStockItem = $this->_stockItemsArray[$productId];
            $productStockItem->assignProduct($product);
        }
        return $this;
    }

    /**
     * Remove stock information from static variable
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function removeInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (($product instanceof Magento_Catalog_Model_Product)
            && $product->getId()
            && isset($this->_stockItemsArray[$product->getId()])) {
            unset($this->_stockItemsArray[$product->getId()]);
        }
        return $this;
    }

    /**
     * Add information about producs stock status to collection
     * Used in for product collection after load
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function addStockStatusToCollection($observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection->hasFlag('require_stock_items')) {
            $this->_stockFactory->create()->addItemsToProducts($productCollection);
        } else {
            $this->_stockStatusFactory->create()->addStockStatusToProducts($productCollection);
        }
        return $this;
    }

    /**
     * Add Stock items to product collection
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function addInventoryDataToCollection($observer)
    {
        $productCollection = $observer->getEvent()->getProductCollection();
        $this->_stockFactory->create()->addItemsToProducts($productCollection);
        return $this;
    }

    /**
     * Saving product inventory data. Product qty calculated dynamically.
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function saveInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (is_null($product->getStockData())) {
            if ($product->getIsChangedWebsites() || $product->dataHasChangedFor('status')) {
                Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')
                    ->updateStatus($product->getId());
            }
            return $this;
        }

        $item = $product->getStockItem();
        if (!$item) {
            $item = $this->_stockItemFactory->create();
        }
        $this->_prepareItemForSave($item, $product);
        $item->save();
        return $this;
    }

    /**
     * Copy product inventory data (used for product duplicate functionality)
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function copyInventoryData($observer)
    {
        /** @var Magento_Catalog_Model_Product $currentProduct */
        $currentProduct = $observer->getEvent()->getCurrentProduct();
        /** @var Magento_Catalog_Model_Product $newProduct */
        $newProduct = $observer->getEvent()->getNewProduct();

        $newProduct->unsStockItem();
        $stockData = array(
            'use_config_min_qty'        => 1,
            'use_config_min_sale_qty'   => 1,
            'use_config_max_sale_qty'   => 1,
            'use_config_backorders'     => 1,
            'use_config_notify_stock_qty'=> 1
        );
        if ($currentStockItem = $currentProduct->getStockItem()) {
            $stockData += array(
                'use_config_enable_qty_inc'  => $currentStockItem->getData('use_config_enable_qty_inc'),
                'enable_qty_increments'             => $currentStockItem->getData('enable_qty_increments'),
                'use_config_qty_increments'         => $currentStockItem->getData('use_config_qty_increments'),
                'qty_increments'                    => $currentStockItem->getData('qty_increments'),
            );
        }
        $newProduct->setStockData($stockData);

        return $this;
    }

    /**
     * Prepare stock item data for save
     *
     * @param Magento_CatalogInventory_Model_Stock_Item $item
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_CatalogInventory_Model_Observer
     */
    protected function _prepareItemForSave($item, $product)
    {
        $item->addData($product->getStockData())
            ->setProduct($product)
            ->setProductId($product->getId())
            ->setStockId($item->getStockId());
        if (!is_null($product->getData('stock_data/min_qty'))
            && is_null($product->getData('stock_data/use_config_min_qty'))) {
            $item->setData('use_config_min_qty', false);
        }
        if (!is_null($product->getData('stock_data/min_sale_qty'))
            && is_null($product->getData('stock_data/use_config_min_sale_qty'))) {
            $item->setData('use_config_min_sale_qty', false);
        }
        if (!is_null($product->getData('stock_data/max_sale_qty'))
            && is_null($product->getData('stock_data/use_config_max_sale_qty'))) {
            $item->setData('use_config_max_sale_qty', false);
        }
        if (!is_null($product->getData('stock_data/backorders'))
            && is_null($product->getData('stock_data/use_config_backorders'))) {
            $item->setData('use_config_backorders', false);
        }
        if (!is_null($product->getData('stock_data/notify_stock_qty'))
            && is_null($product->getData('stock_data/use_config_notify_stock_qty'))) {
            $item->setData('use_config_notify_stock_qty', false);
        }
        $originalQty = $product->getData('stock_data/original_inventory_qty');
        if (strlen($originalQty)>0) {
            $item->setQtyCorrection($item->getQty()-$originalQty);
        }
        if (!is_null($product->getData('stock_data/enable_qty_increments'))
            && is_null($product->getData('stock_data/use_config_enable_qty_inc'))) {
            $item->setData('use_config_enable_qty_inc', false);
        }
        if (!is_null($product->getData('stock_data/qty_increments'))
            && is_null($product->getData('stock_data/use_config_qty_increments'))) {
            $item->setData('use_config_qty_increments', false);
        }
        return $this;

    }

    /**
     * Removes error statuses from quote and item, set by this observer
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @param int $code
     * @return Magento_CatalogInventory_Model_Observer
     */
    protected function _removeErrorsFromQuoteAndItem($item, $code)
    {
        if ($item->getHasError()) {
            $params = array(
                'origin' => 'cataloginventory',
                'code' => $code
            );
            $item->removeErrorInfosByParams($params);
        }

        $quote = $item->getQuote();
        $quoteItems = $quote->getItemsCollection();
        $canRemoveErrorFromQuote = true;

        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getItemId() == $item->getItemId()) {
                continue;
            }

            $errorInfos = $quoteItem->getErrorInfos();
            foreach ($errorInfos as $errorInfo) {
                if ($errorInfo['code'] == $code) {
                    $canRemoveErrorFromQuote = false;
                    break;
                }
            }

            if (!$canRemoveErrorFromQuote) {
                break;
            }
        }

        if ($quote->getHasError() && $canRemoveErrorFromQuote) {
            $params = array(
                'origin' => 'cataloginventory',
                'code' => $code
            );
            $quote->removeErrorInfosByParams(null, $params);
        }

        return $this;
    }

    /**
     * Check product inventory data when quote item quantity declaring
     *
     * @param  Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     * @throws Magento_Core_Exception
     */
    public function checkQuoteItemQty($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        /* @var $quoteItem Magento_Sales_Model_Quote_Item */
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }

        /**
         * Get Qty
         */
        $qty = $quoteItem->getQty();

        /**
         * Check if product in stock. For composite products check base (parent) item stosk status
         */
        $stockItem = $quoteItem->getProduct()->getStockItem();
        $parentStockItem = false;
        if ($quoteItem->getParentItem()) {
            $parentStockItem = $quoteItem->getParentItem()->getProduct()->getStockItem();
        }
        if ($stockItem) {
            if (!$stockItem->getIsInStock() || ($parentStockItem && !$parentStockItem->getIsInStock())) {
                $quoteItem->addErrorInfo(
                    'cataloginventory',
                    Magento_CatalogInventory_Helper_Data::ERROR_QTY,
                    __('This product is out of stock.')
                );
                $quoteItem->getQuote()->addErrorInfo(
                    'stock',
                    'cataloginventory',
                    Magento_CatalogInventory_Helper_Data::ERROR_QTY,
                    __('Some of the products are currently out of stock.')
                );
                return $this;
            } else {
                // Delete error from item and its quote, if it was set due to item out of stock
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Magento_CatalogInventory_Helper_Data::ERROR_QTY);
            }
        }

        /**
         * Check item for options
         */
        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            $qty = $quoteItem->getProduct()->getTypeInstance()->prepareQuoteItemQty($qty, $quoteItem->getProduct());
            $quoteItem->setData('qty', $qty);

            if ($stockItem) {
                $result = $stockItem->checkQtyIncrements($qty);
                if ($result->getHasError()) {
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        Magento_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        Magento_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS,
                        $result->getQuoteMessage()
                    );
                } else {
                    // Delete error from item and its quote, if it was set due to qty problems
                    $this->_removeErrorsFromQuoteAndItem(
                        $quoteItem,
                        Magento_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS
                    );
                }
            }

            foreach ($options as $option) {
                $optionValue = $option->getValue();
                /* @var $option Magento_Sales_Model_Quote_Item_Option */
                $optionQty = $qty * $optionValue;
                $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $optionValue;

                $stockItem = $option->getProduct()->getStockItem();

                if ($quoteItem->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $stockItem->setProductName($quoteItem->getName());
                }

                /* @var $stockItem Magento_CatalogInventory_Model_Stock_Item */
                if (!$stockItem instanceof Magento_CatalogInventory_Model_Stock_Item) {
                    throw new Magento_Core_Exception(__('The stock item for Product in option is not valid.'));
                }

                /**
                 * define that stock item is child for composite product
                 */
                $stockItem->setIsChildItem(true);
                /**
                 * don't check qty increments value for option product
                 */
                $stockItem->setSuppressCheckQtyIncrements(true);

                $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                    $option->getProduct()->getId(),
                    $quoteItem->getId(),
                    $increaseOptionQty
                );

                $result = $stockItem->checkQuoteItemQty($optionQty, $qtyForCheck, $optionValue);

                if (!is_null($result->getItemIsQtyDecimal())) {
                    $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
                }

                if ($result->getHasQtyOptionUpdate()) {
                    $option->setHasQtyOptionUpdate(true);
                    $quoteItem->updateQtyOption($option, $result->getOrigQty());
                    $option->setValue($result->getOrigQty());
                    /**
                     * if option's qty was updates we also need to update quote item qty
                     */
                    $quoteItem->setData('qty', intval($qty));
                }
                if (!is_null($result->getMessage())) {
                    $option->setMessage($result->getMessage());
                    $quoteItem->setMessage($result->getMessage());
                }
                if (!is_null($result->getItemBackorders())) {
                    $option->setBackorders($result->getItemBackorders());
                }

                if ($result->getHasError()) {
                    $option->setHasError(true);

                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        Magento_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        Magento_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getQuoteMessage()
                    );
                } else {
                    // Delete error from item and its quote, if it was set due to qty lack
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, Magento_CatalogInventory_Helper_Data::ERROR_QTY);
                }

                $stockItem->unsIsChildItem();
            }
        } else {
            /* @var $stockItem Magento_CatalogInventory_Model_Stock_Item */
            if (!$stockItem instanceof Magento_CatalogInventory_Model_Stock_Item) {
                throw new Magento_Core_Exception(__('The stock item for Product is not valid.'));
            }

            /**
             * When we work with subitem (as subproduct of bundle or configurable product)
             */
            if ($quoteItem->getParentItem()) {
                $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
                /**
                 * we are using 0 because original qty was processed
                 */
                $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                    $quoteItem->getProduct()->getId(),
                    $quoteItem->getId(),
                    0
                );
            } else {
                $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
                $rowQty = $qty;
                $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                    $quoteItem->getProduct()->getId(),
                    $quoteItem->getId(),
                    $increaseQty
                );
            }

            $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
            if (!is_null($productTypeCustomOption)) {
                // Check if product related to current item is a part of grouped product
                if ($productTypeCustomOption->getValue() == Magento_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                    $stockItem->setProductName($quoteItem->getProduct()->getName());
                    $stockItem->setIsChildItem(true);
                }
            }

            $result = $stockItem->checkQuoteItemQty($rowQty, $qtyForCheck, $qty);

            if ($stockItem->hasIsChildItem()) {
                $stockItem->unsIsChildItem();
            }

            if (!is_null($result->getItemIsQtyDecimal())) {
                $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
                if ($quoteItem->getParentItem()) {
                    $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
                }
            }

            /**
             * Just base (parent) item qty can be changed
             * qty of child products are declared just during add process
             * exception for updating also managed by product type
             */
            if ($result->getHasQtyOptionUpdate()
                && (!$quoteItem->getParentItem()
                    || $quoteItem->getParentItem()->getProduct()->getTypeInstance()
                        ->getForceChildItemQtyChanges($quoteItem->getParentItem()->getProduct())
                )
            ) {
                $quoteItem->setData('qty', $result->getOrigQty());
            }

            if (!is_null($result->getItemUseOldQty())) {
                $quoteItem->setUseOldQty($result->getItemUseOldQty());
            }
            if (!is_null($result->getMessage())) {
                $quoteItem->setMessage($result->getMessage());
            }

            if (!is_null($result->getItemBackorders())) {
                $quoteItem->setBackorders($result->getItemBackorders());
            }

            if ($result->getHasError()) {
                $quoteItem->addErrorInfo(
                    'cataloginventory',
                    Magento_CatalogInventory_Helper_Data::ERROR_QTY,
                    $result->getMessage()
                );

                $quoteItem->getQuote()->addErrorInfo(
                    $result->getQuoteMessageIndex(),
                    'cataloginventory',
                    Magento_CatalogInventory_Helper_Data::ERROR_QTY,
                    $result->getQuoteMessage()
                );
            } else {
                // Delete error from item and its quote, if it was set due to qty lack
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Magento_CatalogInventory_Helper_Data::ERROR_QTY);
            }
        }

        return $this;
    }

    /**
     * Get product qty includes information from all quote items
     * Need be used only in sungleton mode
     *
     * @param int   $productId
     * @param int   $quoteItemId
     * @param float $itemQty
     * @return int
     */
    protected function _getQuoteItemQtyForCheck($productId, $quoteItemId, $itemQty)
    {
        $qty = $itemQty;
        if (isset($this->_checkedQuoteItems[$productId]['qty']) &&
            !in_array($quoteItemId, $this->_checkedQuoteItems[$productId]['items'])) {
                $qty += $this->_checkedQuoteItems[$productId]['qty'];
        }

        $this->_checkedQuoteItems[$productId]['qty'] = $qty;
        $this->_checkedQuoteItems[$productId]['items'][] = $quoteItemId;

        return $qty;
    }

    /**
     * Subtract qtys of quote item products after multishipping checkout
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     */
    public function checkoutAllSubmitAfter(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote->getInventoryProcessed()) {
            $this->subtractQuoteInventory($observer);
            $this->reindexQuoteInventory($observer);
        }
        return $this;
    }

    /**
     * Subtract quote items qtys from stock items related with quote items products.
     *
     * Used before order placing to make order save/place transaction smaller
     * Also called after every successful order placement to ensure subtraction of inventory
     *
     * @param Magento_Event_Observer $observer
     */
    public function subtractQuoteInventory(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        // Maybe we've already processed this quote in some event during order placement
        // e.g. call in event 'sales_model_service_quote_submit_before' and later in 'checkout_submit_all_after'
        if ($quote->getInventoryProcessed()) {
            return;
        }
        $items = $this->_getProductsQty($quote->getAllItems());

        /**
         * Remember items
         */
        $this->_itemsForReindex = Mage::getSingleton('Magento_CatalogInventory_Model_Stock')->registerProductsSale($items);

        $quote->setInventoryProcessed(true);
        return $this;
    }

    /**
     * Revert quote items inventory data (cover not success order place case)
     * @param $observer
     */
    public function revertQuoteInventory($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $items = $this->_getProductsQty($quote->getAllItems());
        Mage::getSingleton('Magento_CatalogInventory_Model_Stock')->revertProductsSale($items);

        // Clear flag, so if order placement retried again with success - it will be processed
        $quote->setInventoryProcessed(false);
    }

    /**
     * Adds stock item qty to $items (creates new entry or increments existing one)
     * $items is array with following structure:
     * array(
     *  $productId  => array(
     *      'qty'   => $qty,
     *      'item'  => $stockItems|null
     *  )
     * )
     *
     * @param Magento_Sales_Model_Quote_Item $quoteItem
     * @param array &$items
     */
    protected function _addItemToQtyArray($quoteItem, &$items)
    {
        $productId = $quoteItem->getProductId();
        if (!$productId)
            return;
        if (isset($items[$productId])) {
            $items[$productId]['qty'] += $quoteItem->getTotalQty();
        } else {
            $stockItem = null;
            if ($quoteItem->getProduct()) {
                $stockItem = $quoteItem->getProduct()->getStockItem();
            }
            $items[$productId] = array(
                'item' => $stockItem,
                'qty'  => $quoteItem->getTotalQty()
            );
        }
    }

    /**
     * Prepare array with information about used product qty and product stock item
     * result is:
     * array(
     *  $productId  => array(
     *      'qty'   => $qty,
     *      'item'  => $stockItems|null
     *  )
     * )
     * @param array $relatedItems
     * @return array
     */
    protected function _getProductsQty($relatedItems)
    {
        $items = array();
        foreach ($relatedItems as $item) {
            $productId  = $item->getProductId();
            if (!$productId) {
                continue;
            }
            $children = $item->getChildrenItems();
            if ($children) {
                foreach ($children as $childItem) {
                    $this->_addItemToQtyArray($childItem, $items);
                }
            } else {
                $this->_addItemToQtyArray($item, $items);
            }
        }
        return $items;
    }

    /**
     * Refresh stock index for specific stock items after successful order placement
     *
     * @param $observer
     */
    public function reindexQuoteInventory($observer)
    {
        // Reindex quote ids
        $quote = $observer->getEvent()->getQuote();
        $productIds = array();
        foreach ($quote->getAllItems() as $item) {
            $productIds[$item->getProductId()] = $item->getProductId();
            $children   = $item->getChildrenItems();
            if ($children) {
                foreach ($children as $childItem) {
                    $productIds[$childItem->getProductId()] = $childItem->getProductId();
                }
            }
        }

        if( count($productIds)) {
            Mage::getResourceSingleton('Magento_CatalogInventory_Model_Resource_Indexer_Stock')
                ->reindexProducts($productIds);
        }

        // Reindex previously remembered items
        $productIds = array();
        foreach ($this->_itemsForReindex as $item) {
            $item->save();
            $productIds[] = $item->getProductId();
        }
        Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product_Indexer_Price')->reindexProductIds($productIds);

        $this->_itemsForReindex = array(); // Clear list of remembered items - we don't need it anymore

        return $this;
    }

    /**
     * Return creditmemo items qty to stock
     *
     * @param Magento_Event_Observer $observer
     */
    public function refundOrderInventory($observer)
    {
        /* @var $creditmemo Magento_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = array();
        foreach ($creditmemo->getAllItems() as $item) {
            /* @var $item Magento_Sales_Model_Order_Creditmemo_Item */
            $return = false;
            if ($item->hasBackToStock()) {
                if ($item->getBackToStock() && $item->getQty()) {
                    $return = true;
                }
            } elseif (Mage::helper('Magento_CatalogInventory_Helper_Data')->isAutoReturnEnabled()) {
                $return = true;
            }
            if ($return) {
                $parentOrderId = $item->getOrderItem()->getParentItemId();
                /* @var $parentItem Magento_Sales_Model_Order_Creditmemo_Item */
                $parentItem = $parentOrderId ? $creditmemo->getItemByOrderId($parentOrderId) : false;
                $qty = $parentItem ? ($parentItem->getQty() * $item->getQty()) : $item->getQty();
                if (isset($items[$item->getProductId()])) {
                    $items[$item->getProductId()]['qty'] += $qty;
                } else {
                    $items[$item->getProductId()] = array(
                        'qty' => $qty,
                        'item'=> null,
                    );
                }
            }
        }
        Mage::getSingleton('Magento_CatalogInventory_Model_Stock')->revertProductsSale($items);
    }

    /**
     * Cancel order item
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function cancelOrderItem($observer)
    {
        $item = $observer->getEvent()->getItem();

        $children = $item->getChildrenItems();
        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();

        if ($item->getId() && ($productId = $item->getProductId()) && empty($children) && $qty) {
            Mage::getSingleton('Magento_CatalogInventory_Model_Stock')->backItemQty($productId, $qty);
        }

        return $this;
    }

    /**
     * Update items stock status and low stock date.
     *
     * @param Magento_Event_Observer $observer
     * @return  Magento_CatalogInventory_Model_Observer
     */
    public function updateItemsStockUponConfigChange($observer)
    {
        Mage::getResourceSingleton('Magento_CatalogInventory_Model_Resource_Stock')->updateSetOutOfStock();
        Mage::getResourceSingleton('Magento_CatalogInventory_Model_Resource_Stock')->updateSetInStock();
        Mage::getResourceSingleton('Magento_CatalogInventory_Model_Resource_Stock')->updateLowStockDate();
        return $this;
    }

    /**
     * Update Only product status observer
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     */
    public function productStatusUpdate(Magento_Event_Observer $observer)
    {
        $productId = $observer->getEvent()->getProductId();
        Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')
            ->updateStatus($productId);
        return $this;
    }

    /**
     * Catalog Product website update
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     */
    public function catalogProductWebsiteUpdate(Magento_Event_Observer $observer)
    {
        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();

        foreach ($websiteIds as $websiteId) {
            foreach ($productIds as $productId) {
                Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')
                    ->updateStatus($productId, null, $websiteId);
            }
        }

        return $this;
    }

    /**
     * Add stock status to prepare index select
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     */
    public function addStockStatusToPrepareIndexSelect(Magento_Event_Observer $observer)
    {
        $website    = $observer->getEvent()->getWebsite();
        $select     = $observer->getEvent()->getSelect();

        Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')
            ->addStockStatusToSelect($select, $website);

        return $this;
    }

    /**
     * Add stock status limitation to catalog product price index select object
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     */
    public function prepareCatalogProductIndexSelect(Magento_Event_Observer $observer)
    {
        $select     = $observer->getEvent()->getSelect();
        $entity     = $observer->getEvent()->getEntityField();
        $website    = $observer->getEvent()->getWebsiteField();

        Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')
            ->prepareCatalogProductIndexSelect($select, $entity, $website);

        return $this;
    }

    /**
     * Reindex all events of product-massAction type
     *
     * @param Magento_Event_Observer $observer
     */
    public function reindexProductsMassAction($observer)
    {
        Mage::getSingleton('Magento_Index_Model_Indexer')->indexEvents(
            Magento_Catalog_Model_Product::ENTITY, Magento_Index_Model_Event::TYPE_MASS_ACTION
        );
    }

    /**
     * Detects whether product status should be shown
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogInventory_Model_Observer
     */
    public function displayProductStatusInfo($observer)
    {
        $info = $observer->getEvent()->getStatus();
        $info->setDisplayStatus(Mage::helper('Magento_CatalogInventory_Helper_Data')->isDisplayProductStockStatus());
        return $this;
    }
}
