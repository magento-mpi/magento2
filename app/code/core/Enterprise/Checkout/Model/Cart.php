<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Admin Checkout processing model
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Model_Cart extends Varien_Object
{
    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_resultErrors = array();

    /**
     * List of currently affected items skus
     *
     * @var array
     */
    protected $_currentlyAffectedItems = array();

    /**
     * Setter for $_customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Enterprise_Checkout_Model_Cart
     */
    public function setCustomer($customer)
    {
        if ($customer instanceof Varien_Object && $customer->getId()) {
            $this->_customer = $customer;
            $this->_quote = null;
        }
        return $this;
    }

    /**
     * Getter for $_customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * Return quote store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Return current active quote for specified customer
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (!is_null($this->_quote)) {
            return $this->_quote;
        }

        $this->_quote = Mage::getModel('sales/quote');

        if ($this->getCustomer() !== null) {
            $this->_quote
                ->setSharedStoreIds($this->getQuoteSharedStoreIds())
                ->loadByCustomer($this->getCustomer()->getId());
        }

        return $this->_quote;
    }

    /**
     * Return quote instance depending on current area
     *
     * @return Mage_Adminhtml_Model_Session_Quote|Mage_Sales_Model_Quote
     */
    public function getActualQuote()
    {
        if ($this->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote();
        } else {
            return $this->getQuote();
        }
    }

    /**
     * Return appropriate store ids for retrieving quote in current store
     * Correct customer shared store ids when customer has Admin Store
     *
     * @return array
     */
    public function getQuoteSharedStoreIds()
    {
        if ($this->getStoreId()) {
            return Mage::app()->getStore($this->getStoreId())
                ->getWebsite()
                ->getStoreIds();
        }
        if (!$this->getCustomer()) {
            return array();
        }
        if ((bool)$this->getCustomer()->getSharingConfig()->isWebsiteScope()) {
            return Mage::app()->getWebsite($this->getCustomer()->getWebsiteId())->getStoreIds();
        } else {
            return $this->getCustomer()->getSharedStoreIds();
        }
    }

    /**
     * Create quote by demand or return active customer quote if it exists
     *
     * @return Mage_Sales_Model_Quote
     */
    public function createQuote()
    {
        if (!$this->getQuote()->getId() && $this->getCustomer() !== null) {
            $this->getQuote()
                ->assignCustomer($this->getCustomer())
                ->save();
        }
        return $this->getQuote();
    }

    /**
     * Recollect quote and save it
     *
     * @param bool $recollect Collect quote totals or not
     * @return Enterprise_Checkout_Model_Cart
     */
    public function saveQuote($recollect = true)
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }
        if ($recollect) {
            $this->getQuote()->collectTotals();
        }
        $this->getQuote()->save();
        return $this;
    }

    /**
     * Return preferred non-admin store Id
     * If Customer has active quote - return its store, otherwise try to get customer store or default store
     *
     * @return int|bool
     */
    public function getPreferredStoreId()
    {
        $storeId = false;
        $quote = $this->getQuote();
        $customer = $this->getCustomer();

        if ($quote->getId() && $quote->getStoreId()) {
            $storeId = $quote->getStoreId();
        } elseif ($customer !== null && $customer->getStoreId() && !$customer->getStore()->isAdmin()) {
            $storeId = $customer->getStoreId();
        } else {
            $customerStoreIds = $this->getQuoteSharedStoreIds(); //$customer->getSharedStoreIds();
            $storeId = array_shift($customerStoreIds);
            if (Mage::app()->getStore($storeId)->isAdmin()) {
                $defaultStore = Mage::app()->getAnyStoreView();
                if ($defaultStore) {
                    $storeId = $defaultStore->getId();
                }
            }
        }

        return $storeId;
    }

    /**
     * Add product to current order quote
     *
     * $config can be integer qty (older behaviour, when no product configuration was possible)
     * or it can be array of options (newer behaviour).
     *
     * In case of older behaviour same product ids are not added, but quote item qty is increased.
     * In case of newer behaviour same product ids with different configs are added as separate quote items.
     *
     * @param   mixed $product
     * @param   Varien_Object|array|float $config
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function addProduct($product, $config = 1)
    {
        if (is_array($config) || ($config instanceof Varien_Object)) {
            $config = is_array($config) ? new Varien_Object($config) : $config;
            $qty = (float) $config->getQty();
            $separateSameProducts = true;
        } else {
            $qty = (float) $config;
            $config = new Varien_Object();
            $config->setQty($qty);
            $separateSameProducts = false;
        }

        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $productId = $product;
            $product = Mage::getModel('catalog/product')
                ->setStore($this->getStore())
                ->setStoreId($this->getStore()->getId())
                ->load($product);
            if (!$product->getId()) {
                Mage::throwException(
                    Mage::helper('adminhtml')->__('Failed to add a product to cart by id "%s".', $productId)
                );
            }
        }

        if ($product->getStockItem()) {
            if (!$product->getStockItem()->getIsQtyDecimal()) {
                $qty = (int)$qty;
            } else {
                $product->setIsQtyDecimal(1);
            }
        }
        $qty = $qty > 0 ? $qty : 1;

        $item = null;
        if (!$separateSameProducts) {
            $item = $this->getQuote()->getItemByProduct($product);
        }
        if ($item) {
            $item->setQty($item->getQty() + $qty);
        } else {
            $item = $this->getQuote()->addProduct($product, $config);
            if (is_string($item)) {
                Mage::throwException($item);
            }
            $item->checkData();
        }

        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add new item to quote based on existing order Item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return Mage_Sales_Model_Quote_Item
     * @throws Mage_Core_Exception
     */
    public function reorderItem(Mage_Sales_Model_Order_Item $orderItem, $qty = 1)
    {
        if (!$orderItem->getId()) {
            Mage::throwException(Mage::helper('enterprise_checkout')->__('Failed to reorder item'));
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->getStore()->getId())
            ->load($orderItem->getProductId());

        if ($product->getId()) {
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new Varien_Object($info);
            $product->setSkipCheckRequiredOption(true);
            $item = $this->createQuote()->addProduct($product, $info);
            if (is_string($item)) {
                Mage::throwException($item);
            }

            $item->setQty($qty);

            if ($additionalOptions = $orderItem->getProductOptionByCode('additional_options')) {
                $item->addOption(new Varien_Object(
                    array(
                        'product'   => $item->getProduct(),
                        'code'      => 'additional_options',
                        'value'     => serialize($additionalOptions)
                    )
                ));
            }

            Mage::dispatchEvent('sales_convert_order_item_to_quote_item', array(
                'order_item' => $orderItem,
                'quote_item' => $item
            ));

            return $item;

        } else {
            Mage::throwException(Mage::helper('enterprise_checkout')->__('Failed to add a product of order item'));
        }
    }

    /**
     * Adds error of operation either to internal array or directly to session (if set)
     *
     * @param string $message
     * @return Enterprise_Checkout_Model_Cart
     */
    protected function _addResultError($message)
    {
        $session = $this->getSession();
        if ($session) {
            $session->addError($message);
        } else {
            $this->_resultErrors[] = $message;
        }
        return $this;
    }

    /**
     * Returns array of errors encountered during previous operations
     *
     * @return array
     */
    protected function getResultErrors()
    {
        return $this->_resultErrors;
    }

    /**
     * Clears array of operation errors, so caller will get only errors related to last operation
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    protected function clearResultErrors()
    {
        $this->_resultErrors = array();
        return $this;
    }

    /**
     * Add multiple products to current order quote.
     * Errors can be received via getResultErrors() or directly into session if it was set via setSession().
     *
     * @param   array $products
     * @return  Enterprise_Checkout_Model_Cart|Exception
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (float)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            } catch (Mage_Core_Exception $e) {
                $this->_addResultError($e->getMessage());
            } catch (Exception $e) {
                return $e;
            }
        }

        return $this;
    }

    /**
     * Remove items from quote or move them to wishlist etc.
     *
     * @param array $data Array of items
     * @return Enterprise_Checkout_Model_Cart
     */
    public function updateQuoteItems($data)
    {
        if (!$this->getQuote()->getId() || !is_array($data)) {
            return $this;
        }

        foreach ($data as $itemId => $info) {
            if (!empty($info['configured'])) {
                $item = $this->getQuote()->updateItem($itemId, new Varien_Object($info));
                $itemQty = (float) $item->getQty();
            } else {
                $item = $this->getQuote()->getItemById($itemId);
                $itemQty = (float) $info['qty'];
            }

            if ($item && $item->getProduct()->getStockItem()) {
                if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                    $itemQty = (int) $itemQty;
                } else {
                    $item->setIsQtyDecimal(1);
                }
            }

            $itemQty = ($itemQty > 0) ? $itemQty : 1;
            if (isset($info['custom_price'])) {
                $itemPrice = $this->_parseCustomPrice($info['custom_price']);
            } else {
                $itemPrice = null;
            }
            $noDiscount = !isset($info['use_discount']);

            if (empty($info['action']) || !empty($info['configured'])) {
                if ($item) {
                    $item->setQty($itemQty);
                    $item->setCustomPrice($itemPrice);
                    $item->setOriginalCustomPrice($itemPrice);
                    $item->setNoDiscount($noDiscount);
                    $item->getProduct()->setIsSuperMode(true);
                    $item->checkData();
                }
            } else {
                $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
            }
        }
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()
                ->collectTotals()
                ->save();
        }
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Move quote item to wishlist.
     * Errors can be received via getResultErrors() or directly into session if it was set via setSession().
     *
     * @param Mage_Sales_Model_Quote_Item|int $item
     * @param string $moveTo Destination storage
     * @return Enterprise_Checkout_Model_Cart
     */
    public function moveQuoteItem($item, $moveTo)
    {
        $item = $this->_getQuoteItem($item);
        if ($item) {
            switch ($moveTo) {
                case 'wishlist':
                    $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($this->getCustomer(), true)
                        ->setStore($this->getStore())
                        ->setSharedStoreIds($this->getStore()->getWebsite()->getStoreIds());
                    if ($wishlist->getId() && $item->getProduct()->isVisibleInSiteVisibility()) {
                        $wishlistItem = $wishlist->addNewItem($item->getProduct(), $item->getBuyRequest());
                        if (is_string($wishlistItem)) {
                            $this->_addResultError($wishlistItem);
                        } else if ($wishlistItem->getId()) {
                            $this->getQuote()->removeItem($item->getId());
                        }
                    }
                    break;
                default:
                    $this->getQuote()->removeItem($item->getId());
                    break;
            }
        }
        return $this;
    }

    /**
     * Create duplicate of quote preserving all data (items, addresses, payment etc.)
     *
     * @param Mage_Sales_Model_Quote $quote Original Quote
     * @param bool $active Create active quote or not
     * @return Mage_Sales_Model_Quote New created quote
     */
    public function copyQuote(Mage_Sales_Model_Quote $quote, $active = false)
    {
        if (!$quote->getId()) {
            return $quote;
        }
        $newQuote = clone $quote;
        $newQuote->setId(null);
        $newQuote->setIsActive($active ? 1 : 0);
        $newQuote->save();

        // copy items with their options
        $newParentItemIds = array();
        foreach ($quote->getItemsCollection() as $item) {
            // save child items later
            if ($item->getParentItem()) {
                continue;
            }
            $oldItemId = $item->getId();
            $newItem = clone $item;
            $newItem->setQuote($newQuote);
            $newItem->save();
            $newParentItemIds[$oldItemId] = $newItem->getId();
        }

        // save childs with new parent id
        foreach ($quote->getItemsCollection() as $item) {
            if (!$item->getParentItem() || !isset($newParentItemIds[$item->getParentItemId()])) {
                continue;
            }
            $newItem = clone $item;
            $newItem->setQuote($newQuote);
            $newItem->setParentItemId($newParentItemIds[$item->getParentItemId()]);
            $newItem->save();
        }

        // copy billing and shipping addresses
        foreach ($quote->getAddressesCollection() as $address) {
            $address->setQuote($newQuote);
            $address->setId(null);
            $address->save();
        }

        // copy payment info
        foreach ($quote->getPaymentsCollection() as $payment) {
            $payment->setQuote($newQuote);
            $payment->setId(null);
            $payment->save();
        }

        return $newQuote;
    }

    /**
     * Wrapper for getting quote item
     *
     * @param Mage_Sales_Model_Quote_Item|int $item
     * @return Mage_Sales_Model_Quote_Item|bool
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            return $item;
        }
        elseif (is_numeric($item)) {
            return $this->getQuote()->getItemById($item);
        }
        return false;
    }

    /**
     * Add single item to stack and return extended pushed item. For return format see _addAffectedItem()
     *
     * @param string $sku
     * @param float  $qty
     * @param array  $config Configuration data of the product (if has been configured)
     * @return array
     */
    public function prepareAddProductBySku($sku, $qty, $config = array())
    {
        $checkedItem = $this->checkItem($sku, $qty, $config);
        $code = $checkedItem['code'];
        unset($checkedItem['code']);
        return $this->_addAffectedItem($checkedItem, $code);
    }

    /**
     * Check submitted SKUs
     *
     * @see saveAffectedProducts()
     * @param array $items Example: [['sku' => 'simple1', 'qty' => 2], ['sku' => 'simple2', 'qty' => 3], ...]
     * @return Enterprise_Checkout_Model_Cart
     */
    public function prepareAddProductsBySku($items)
    {
        foreach ($items as $item) {
            if (!isset($item['sku']) || !isset($item['qty'])) {
                continue;
            }
            $this->prepareAddProductBySku($item['sku'], $item['qty']);
        }
        return $this;
    }

    /**
     * Checks whether requested quantity is allowed taking into account that some amount already added to quote.
     * Returns TRUE if everything is okay
     * Returns array in below format on error:
     * [
     *  'status' => string (see Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_* constants),
     *  'qty_max_allowed' => int (optional, if 'status'==ADD_ITEM_STATUS_FAILED_QTY_ALLOWED)
     * ]
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $stockItem
     * @param Mage_Catalog_Model_Product             $product
     * @param float                                  $requestedQty
     * @return array|true
     */
    public function getQtyStatus(
        Mage_CatalogInventory_Model_Stock_Item $stockItem,
        Mage_Catalog_Model_Product $product,
        $requestedQty
    ) {
        if (!$stockItem->getIsInStock()) {
            return array('status' => Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK);
        }
        if (!$stockItem->getManageStock()) {
            return true;
        }
        if ($stockItem->getBackorders() != Mage_CatalogInventory_Model_Stock::BACKORDERS_NO) {
            return true;
        }
        $quoteItem = $this->getActualQuote()->getItemByProduct($product);
        $isAdmin = $this->getStore()->isAdmin();

        if ($stockItem->getMinSaleQty() && !$isAdmin) {
            $minAllowedQty = $stockItem->getMinSaleQty();
            $status = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART;
        } else {
            $minAllowedQty = 1;
        }

        if ($requestedQty < $minAllowedQty) {
            $status = empty($status) ? Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED : $status;
            return array('status' => $status, 'qty_min_allowed' => $minAllowedQty);
        }

        if ($stockItem->getMaxSaleQty() && !$isAdmin) {
            $maxAllowedQty = min($stockItem->getStockQty(), $stockItem->getMaxSaleQty());
            $status = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART;
        } else {
            $maxAllowedQty = $stockItem->getStockQty();
        }

        $allowedQty = $quoteItem ? ($maxAllowedQty - $quoteItem->getQty()) : $maxAllowedQty;

        if ($allowedQty <= 0 || $allowedQty < $minAllowedQty) {
            // All available quantity already added to quote
            return array('status' => Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK);
        } else if ($requestedQty > $allowedQty) {
            $status = empty($status) ? Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED : $status;
            // Quantity added to quote and requested quantity exceeds available in stock or maximum salable quantity
            return array('status' => $status, 'qty_max_allowed' => $allowedQty);
        } else {
            return true;
        }
    }

    /**
     * Decide whether product has been configured or not
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $config
     * @return bool
     */
    protected function _isConfigured(Mage_Catalog_Model_Product $product, $config)
    {
        // If below POST fields were submitted - this is product's options, it has been already configured
        switch ($product->getTypeId()) {
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                return isset($config['super_attribute']);
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
                return isset($config['bundle_option']);
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                return isset($config['super_group']);
            case Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD:
                return isset($config['giftcard_amount']);
            case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:
                return isset($config['links']);
        }
        return false;
    }

    /**
     * Check item before adding by SKU
     *
     * @param string $sku
     * @param float  $qty
     * @param array  $config Configuration data of the product (if has been configured)
     * @return array
     */
    public function checkItem($sku, $qty, $config = array())
    {
        $item = array('sku' => $sku, 'qty' => is_array($qty) ? (float)$qty['qty'] : ($qty ? (float)$qty : 1));
        if ($item['qty'] < 0) {
            $item['qty'] = 1;
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['sku']);
        if ($product && $product->getId()) {
            $item['id'] = $product->getId();

            if (true === $product->getDisableAddToCart()) {
                $item['code'] = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_PERMISSIONS;
                return $item;
            }

            /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->loadByProduct($product);
            $stockItem->setProduct($product);
            if ($this->_shouldBeConfigured($product)) {
                if ($this->_isConfigured($product, $config)) {
                    $status = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS;
                } else {
                    $status = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE;
                }
            }

            if (empty($status)) {
                $qtyStatus = $this->getQtyStatus($stockItem, $product, $item['qty']);
                if ($qtyStatus === true) {
                    $status = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS;
                } else {
                    $status = $qtyStatus['status'];
                    unset($qtyStatus['status']);
                    // Add qty_max_allowed and qty_min_allowed, if present
                    $item = array_merge($item, $qtyStatus);
                }
            }
        } else {
            $status = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU;
        }
        $item['code'] = $status;
        return $item;
    }

    /**
     * Check whether specified product should be configured
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _shouldBeConfigured($product)
    {
        if ($product->isComposite() || $product->getRequiredOptions()) {
            return true;
        }

        switch ($product->getTypeId()) {
            case Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD:
            case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:
                return true;
        }

        return false;
    }

    /**
     * Add products previously successfully processed by prepareAddProductsBySku() to cart
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    public function saveAffectedProducts()
    {
        $affectedItems = $this->getAffectedItems();
        foreach ($affectedItems as &$item) {
            if ($item['code'] == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS) {
                $this->_safeAddProduct($item);
            }
        }
        $this->setAffectedItems($affectedItems);
        $this->_getCart()->save();
        return $this;
    }

    /**
     * Safely add product to cart, revert cart in error case
     *
     * @param array $item
     * @return Enterprise_Checkout_Model_Cart
     */
    protected function _safeAddProduct(&$item)
    {
        $cart = $this->_getCart();
        $quote = $cart->getQuote();

        // copy data to temporary quote
        /** @var $temporaryQuote Mage_Sales_Model_Quote */
        $temporaryQuote = Mage::getModel('sales/quote');
        foreach ($quote->getAllItems() as $quoteItem) {
            $temporaryItem = clone $quoteItem;
            $temporaryItem->setQuote($temporaryQuote);
            $temporaryQuote->addItem($temporaryItem);
        }
        $cart->setData('quote', $temporaryQuote);
        $success = true;

        try {
            $cart->addProduct($item['item']['id'], $item['item']['qty']);
        } catch (Mage_Core_Exception $e) {
            $success = false;
            $item['code'] = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_UNKNOWN;
            $item['error'] = $e->getMessage();
        } catch (Exception $e) {
            $success = false;
            $item['code'] = Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_UNKNOWN;
            $item['error'] = Mage::helper('enterprise_checkout')->__('The product cannot be added to cart.');
        }

        if ($success) {
            // copy temporary data to real quote
            foreach ($quote->getAllItems() as $quoteItem) {
                $quote->removeItem($quoteItem->getId());
            }
            foreach ($temporaryQuote->getAllItems() as $quoteItem) {
                $quoteItem->setQuote($quote);
                $quote->addItem($quoteItem);
            }
        }
        $cart->setData('quote', $quote);

        return $this;
    }

    /**
     * Returns affected items
     * Return format:
     * sku(string) => [
     *  'item' => [
     *      'sku'             => string,
     *      'qty'             => int,
     *      'id'              => int (optional, if product does exist),
     *      'qty_max_allowed' => int (optional, if 'code'==ADD_ITEM_STATUS_FAILED_QTY_ALLOWED)
     *  ],
     *  'code' => string (see Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_*)
     * ]
     *
     * @see prepareAddProductsBySku()
     * @param null|int $storeId
     * @return array
     */
    public function getAffectedItems($storeId = null)
    {
        $storeId = (is_null($storeId)) ? Mage::app()->getStore()->getId() : (int)$storeId;
        $affectedItems = $this->_getHelper()->getSession()->getAffectedItems();

        return (isset($affectedItems[$storeId]) && is_array($affectedItems[$storeId]))
                ? $affectedItems[$storeId]
                : array();
    }

    /**
     * Returns only items with 'success' status
     *
     * @return array
     */
    public function getSuccessfulAffectedItems()
    {
        $items = array();
        foreach ($this->getAffectedItems() as $item) {
            if ($item['code'] == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Set affected items
     *
     * @param array $items
     * @param null|int $storeId
     * @return Enterprise_Checkout_Model_Cart
     */
    public function setAffectedItems($items, $storeId = null)
    {
        $storeId = (is_null($storeId)) ? Mage::app()->getStore()->getId() : (int)$storeId;
        $affectedItems = $this->_getHelper()->getSession()->getAffectedItems();
        if (!is_array($affectedItems)) {
            $affectedItems = array();
        }

        $affectedItems[$storeId] = $items;
        $this->_getHelper()->getSession()->setAffectedItems($affectedItems);
        return $this;
    }

    /**
     * Retrieve info message
     *
     * @return array
     */
    public function getMessages()
    {
        $affectedItems = $this->getAffectedItems();
        $currentlyAffectedItemsCount  = count($this->_currentlyAffectedItems);
        $currentlyFailedItemsCount = 0;

        foreach ($this->_currentlyAffectedItems as $sku) {
            if (!isset($affectedItems[$sku])
                || $affectedItems[$sku]['code'] != Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS
            ) {
                $currentlyFailedItemsCount++;
            }
        }

        $addedItemsCount = $currentlyAffectedItemsCount - $currentlyFailedItemsCount;

        $failedItemsCount = count($this->getFailedItems());
        $messages = array();
        if ($addedItemsCount) {
            $message = ($addedItemsCount == 1)
                    ? Mage::helper('enterprise_checkout')->__('%s product was added to your shopping cart.', $addedItemsCount)
                    : Mage::helper('enterprise_checkout')->__('%s products were added to your shopping cart.', $addedItemsCount);
            $messages[] = Mage::getSingleton('core/message')->success($message);
        }
        if ($failedItemsCount) {
            $warning = ($failedItemsCount == 1)
                    ? Mage::helper('enterprise_checkout')->__('%s product requires your attention.', $failedItemsCount)
                    : Mage::helper('enterprise_checkout')->__('%s products require your attention.', $failedItemsCount);
            $messages[] = Mage::getSingleton('core/message')->error($warning);
        }
        return $messages;
    }

    /**
     * Retrieve list of failed items. For return format see getAffectedItems().
     *
     * @return array
     */
    public function getFailedItems()
    {
        $failedItems = array();
        foreach ($this->getAffectedItems() as $item) {
            if ($item['code'] != Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS) {
                $failedItems[] = $item;
            }
        }
        return $failedItems;
    }

    /**
     * Add processed item to stack.
     * Return format:
     * [
     *  'item' => [
     *      'sku'             => string,
     *      'qty'             => int,
     *      'id'              => int (optional, if product does exist),
     *      'qty_max_allowed' => int (optional, if 'code'==ADD_ITEM_STATUS_FAILED_QTY_ALLOWED)
     *  ],
     *  'code' => string (see Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_*)
     * ]
     *
     * @param array $item
     * @param string $code
     * @return array
     */
    protected function _addAffectedItem($item, $code)
    {
        if (empty($item['sku'])) {
            return $this;
        }
        $sku = $item['sku'];
        $affectedItems = $this->getAffectedItems();

        if (isset($affectedItems[$item['sku']])) {
            $affectedItems[$sku]['item']['qty'] += $item['qty'];
            $affectedItems[$sku]['code'] = $code;
            unset($item['qty']);
            $affectedItems[$sku]['item'] = array_merge($affectedItems[$sku]['item'], $item);
        } else {
            $affectedItems[$sku] = array('item' => $item, 'code' => $code);
        }

        $this->_currentlyAffectedItems[] = $sku;
        $this->setAffectedItems($affectedItems);
        return $affectedItems[$sku];
    }

    /**
     * Update qty of specified item
     *
     * @param string $sku
     * @param int $qty
     * @return Enterprise_Checkout_Model_Cart
     */
    public function updateItemQty($sku, $qty)
    {
        $affectedItems = $this->getAffectedItems();
        if (isset($affectedItems[$sku])) {
            $affectedItems[$sku]['item']['qty'] = $qty;
        }
        $this->setAffectedItems($affectedItems);
        return $this;
    }

    /**
     * Remove item from storage by specified key(sku)
     *
     * @param string $sku
     * @return bool
     */
    public function removeAffectedItem($sku)
    {
        $affectedItems = $this->getAffectedItems();
        if (isset($affectedItems[$sku])) {
            unset($affectedItems[$sku]);
            $this->setAffectedItems($affectedItems);
            return true;
        }
        return false;
    }

    /**
     * Remove all affected items from storage
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    public function removeAllAffectedItems()
    {
        $this->setAffectedItems(array());
        return $this;
    }

    /**
     * Remove all affected items with code=success
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    public function removeSuccessItems()
    {
        $affectedItems = $this->getAffectedItems();
        foreach ($affectedItems as $key => $item) {
            if ($item['code'] == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_SUCCESS) {
                unset($affectedItems[$key]);
            }
        }
        $this->setAffectedItems($affectedItems);
        return $this;
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Retrieve helper instance
     *
     * @return Enterprise_Checkout_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('enterprise_checkout');
    }

    /**
     * Sets session where data is going to be stored
     *
     * @param Mage_Core_Model_Session_Abstract $session
     * @return Enterprise_Checkout_Model_Cart
     */
    public function setSession(Mage_Core_Model_Session_Abstract $session)
    {
        $this->_getHelper()->setSession($session);
        return $this;
    }

    /**
     * Returns current session used to store data about affected items
     *
     * @return Mage_Core_Model_Session_Abstract
     */
    public function getSession()
    {
        return $this->_getHelper()->getSession();
    }
}
