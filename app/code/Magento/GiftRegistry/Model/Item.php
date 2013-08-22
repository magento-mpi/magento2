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
 * Entity items data model
 *
 * @method Magento_GiftRegistry_Model_Resource_Item _getResource()
 * @method Magento_GiftRegistry_Model_Resource_Item getResource()
 * @method Magento_GiftRegistry_Model_Item setEntityId(int $value)
 * @method int getProductId()
 * @method Magento_GiftRegistry_Model_Item setProductId(int $value)
 * @method float getQty()
 * @method Magento_GiftRegistry_Model_Item setQty(float $value)
 * @method float getQtyFulfilled()
 * @method Magento_GiftRegistry_Model_Item setQtyFulfilled(float $value)
 * @method string getNote()
 * @method Magento_GiftRegistry_Model_Item setNote(string $value)
 * @method string getAddedAt()
 * @method Magento_GiftRegistry_Model_Item setAddedAt(string $value)
 * @method string getCustomOptions()
 * @method Magento_GiftRegistry_Model_Item setCustomOptions(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Item extends Magento_Core_Model_Abstract
    implements Magento_Catalog_Model_Product_Configuration_Item_Interface
{

    /**
     * List of options related to item
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Assoc array of item options
     * Option codes are used as array keys
     *
     * @var array
     */
    protected $_optionsByCode = array();

    /**
     * Flag stating that options were successfully saved
     *
     */
    protected $_flagOptionsSaved = null;

    function _construct() {
        $this->_init('Magento_GiftRegistry_Model_Resource_Item');
    }

    /**
     * Load item by registry id and product id
     *
     * @param int $registryId
     * @param int $productId
     * @return Magento_GiftRegistry_Model_Item
     */
    public function loadByProductRegistry($registryId, $productId)
    {
        $this->_getResource()->loadByProductRegistry($this, $registryId, $productId);
        return $this;
    }

    /**
     * Add or Move item product to shopping cart
     *
     * Return true if product was successful added or exception with code
     * Return false for disabled or unvisible products
     *
     * @throws Magento_Core_Exception
     * @param Magento_Checkout_Model_Cart $cart
     * @param int $qty
     * @return bool
     */
    public function addToCart(Magento_Checkout_Model_Cart $cart, $qty)
    {
        $product = $this->_getProduct();
        $storeId = $this->getStoreId();

        if ($this->getQty() < ($qty + $this->getQtyFulfilled())) {
            $qty = $this->getQty() - $this->getQtyFulfilled();
        }

        if ($product->getStatus() != Magento_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return false;
        }

        if (!$product->isVisibleInSiteVisibility()) {
            if ($product->getStoreId() == $storeId) {
                return false;
            }
            $urlData = Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Url')
                ->getRewriteByProductStore(array($product->getId() => $storeId));
            if (!isset($urlData[$product->getId()])) {
                return false;
            }
            $product->setUrlDataObject(new Magento_Object($urlData));
            $visibility = $product->getUrlDataObject()->getVisibility();
            if (!in_array($visibility, $product->getVisibleInSiteVisibilities())) {
                return false;
            }
        }

        if (!$product->isSalable()) {
            Mage::throwException(
                __('This product(s) is out of stock.'));
        }

        $product->setGiftregistryItemId($this->getId());
        $product->addCustomOption('giftregistry_id', $this->getEntityId());
        $request = $this->getBuyRequest();
        $request->setQty($qty);

        $cart->addProduct($product, $request);
        $relatedProduct = $request->getRelatedProduct();
        if (!empty($relatedProduct)) {
            $cart->addProductsByIds(explode(',', $relatedProduct));
        }

        if (!$product->isVisibleInSiteVisibility()) {
            $cart->getQuote()->getItemByProduct($product)->setStoreId($storeId);
        }
    }

    /**
     * Check product representation in item
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  bool
     */
    public function isRepresentProduct($product)
    {
        if ($this->getProductId() != $product->getId()) {
            return false;
        }

        $itemOptions = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if (!$this->_compareOptions($itemOptions, $productOptions)) {
            return false;
        }
        if (!$this->_compareOptions($productOptions, $itemOptions)) {
            return false;
        }
        return true;
    }

    /**
     * Check if two option sets are identical
     *
     * @param array $options1
     * @param array $options2
     * @return bool
     */
    protected function _compareOptions($options1, $options2)
    {
        $skipOptions = array('qty','info_buyRequest');
        foreach ($options1 as $option) {
            $code = $option->getCode();
            if (in_array($code, $skipOptions)) {
                continue;
            }
            if ( !isset($options2[$code])
                || ($options2[$code]->getValue() === null)
                || $options2[$code]->getValue() != $option->getValue()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set product attributes to item
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_GiftRegistry_Model_Item
     */
    public function setProduct($product)
    {
        $this->setName($product->getName());
        $this->setData('product', $product);
        return $this;
    }

    /**
     * Return product url
     *
     * @return bool
     */
    public function getProductUrl()
    {
        return $this->getProduct()->getProductUrl();
    }

    /**
     * Return item product
     *
     * @return Magento_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (!$this->_getData('product')) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->load($this->getProductId());
            if (!$product->getId()) {
                Mage::throwException(
                    __('Please correct the product for adding the item to the quote.'));
            }
            $this->setProduct($product);
        }
        return $this->_getData('product');
    }

    /**
     * Return item product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_getProduct();
    }

    /**
     * Checks if item model has data changes
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /**
     * Save item options after item is saved
     *
     * @return Magento_GiftRegistry_Model_Item
     */
    protected function _afterSave()
    {
        $this->_saveItemOptions();
        return parent::_afterSave();
    }

    /**
     * Save item options
     *
     * @return Magento_GiftRegistry_Model_Item
     */
    protected function _saveItemOptions()
    {
        foreach ($this->_options as $index => $option) {
            if ($option->isDeleted()) {
                $option->delete();
                unset($this->_options[$index]);
                unset($this->_optionsByCode[$option->getCode()]);
            } else {
                $option->save();
            }
        }

        $this->_flagOptionsSaved = true; // Report to watchers that options were saved

        return $this;
    }

    /**
     * Save model plus its options
     * Ensures saving options in case when resource model was not changed
     */
    public function save()
    {
        $hasDataChanges = $this->hasDataChanges();
        $this->_flagOptionsSaved = false;

        parent::save();

        if ($hasDataChanges && !$this->_flagOptionsSaved) {
            $this->_saveItemOptions();
        }
    }

    /**
     * Initialize item options
     *
     * @param array $options
     * @return Magento_GiftRegistry_Model_Item
     */
    public function setOptions($options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
        return $this;
    }

    /**
     * Retrieve all item options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Retrieve all item options as assoc array with option codes as array keys
     *
     * @return array
     */
    public function getOptionsByCode()
    {
        return $this->_optionsByCode;
    }

    /**
     * Remove option from item options
     *
     * @param string $code
     * @return Magento_GiftRegistry_Model_Item
     */
    public function removeOption($code)
    {
        $option = $this->getOptionByCode($code);
        if ($option) {
            $option->isDeleted(true);
        }
        return $this;
    }

    /**
     * Add option to item
     *
     * @throws  Magento_Core_Exception
     * @param   Magento_GiftRegistry_Model_Item_Option $option
     * @return  Magento_GiftRegistry_Model_Item
     */
    public function addOption($option)
    {
        if (is_array($option)) {
            $option = Mage::getModel('Magento_GiftRegistry_Model_Item_Option')->setData($option)
                ->setItem($this);
        } elseif ($option instanceof Magento_Sales_Model_Quote_Item_Option) {
            // import data from existing quote item option
            $option = Mage::getModel('Magento_GiftRegistry_Model_Item_Option')->setProduct($option->getProduct())
               ->setCode($option->getCode())
               ->setValue($option->getValue())
               ->setItem($this);
        } elseif (($option instanceof Magento_Object)
            && !($option instanceof Magento_GiftRegistry_Model_Item_Option)
        ) {
            $option = Mage::getModel('Magento_GiftRegistry_Model_Item_Option')->setData($option->getData())
               ->setProduct($option->getProduct())
               ->setItem($this);
        } elseif($option instanceof Magento_GiftRegistry_Model_Item_Option) {
            $option->setItem($this);
        } else {
            Mage::throwException(__('Please correct the item option format.'));
        }

        $exOption = $this->getOptionByCode($option->getCode());
        if (!is_null($exOption)) {
            $exOption->addData($option->getData());
        } else {
            $this->_addOptionCode($option);
            $this->_options[] = $option;
        }
        return $this;
    }

    /**
     * Register option code
     *
     * @throws  Magento_Core_Exception
     * @param   Magento_GiftRegistry_Model_Item_Option $option
     * @return  Magento_GiftRegistry_Model_Item
     */
    protected function _addOptionCode($option)
    {
        if (!isset($this->_optionsByCode[$option->getCode()])) {
            $this->_optionsByCode[$option->getCode()] = $option;
        } else {
            Mage::throwException(__('An item option with code %1 already exists.', $option->getCode()));
        }
        return $this;
    }

    /**
     * Retrieve item option by code
     *
     * @param   string $code
     * @return  Magento_GiftRegistry_Model_Item_Option|null
     */
    public function getOptionByCode($code)
    {
        if (isset($this->_optionsByCode[$code]) && !$this->_optionsByCode[$code]->isDeleted()) {
            return $this->_optionsByCode[$code];
        }
        return null;
    }

    /**
     * Returns formatted buy request - object, holding request received from
     * product view page with keys and options for configured product
     *
     * @return Magento_Object
     */
    public function getBuyRequest()
    {
        $option = $this->getOptionByCode('info_buyRequest');
        $buyRequest = new Magento_Object($option ? unserialize($option->getValue()) : null);
        $buyRequest->setOriginalQty($buyRequest->getQty())
            ->setQty($this->getQty() * 1); // Qty value that is stored in buyRequest can be out-of-date
        return $buyRequest;
    }

    /**
     * Clone gift registry item
     *
     * @return Magento_GiftRegistry_Model_Item
     */
    public function __clone()
    {
        $options = $this->getOptions();
        $this->_options = array();
        $this->_optionsByCode = array();
        foreach ($options as $option) {
            $this->addOption(clone $option);
        }
        return $this;
    }

    /**
     * Returns special download params (if needed) for custom option with type = 'file'
     * Needed to implement Magento_Catalog_Model_Product_Configuration_Item_Interface.
     * Currently returns null, as far as we don't show file options and don't need controllers to give file.
     *
     * @return null|Magento_Object
     */
    public function getFileDownloadParams()
    {
        return null;
    }

    /**
     * Validates and sets quantity for the related product
     *
     * @param int|float $quantity New item quantity
     * @throws Magento_Core_Exception
     * @return Magento_GiftRegistry_Model_Item
     */
    public function setQty($quantity)
    {
        $quantity = (float)$quantity;

        if (!$this->_getProduct()->getTypeInstance()->canUseQtyDecimals()) {
            $quantity = round($quantity);
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        return $this->setData('qty', $quantity);
    }
}
