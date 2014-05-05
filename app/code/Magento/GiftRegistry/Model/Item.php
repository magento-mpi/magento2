<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model;

/**
 * Entity items data model
 *
 * @method \Magento\GiftRegistry\Model\Resource\Item _getResource()
 * @method \Magento\GiftRegistry\Model\Resource\Item getResource()
 * @method \Magento\GiftRegistry\Model\Item setEntityId(int $value)
 * @method int getProductId()
 * @method \Magento\GiftRegistry\Model\Item setProductId(int $value)
 * @method float getQty()
 * @method float getQtyFulfilled()
 * @method \Magento\GiftRegistry\Model\Item setQtyFulfilled(float $value)
 * @method string getNote()
 * @method \Magento\GiftRegistry\Model\Item setNote(string $value)
 * @method string getAddedAt()
 * @method \Magento\GiftRegistry\Model\Item setAddedAt(string $value)
 * @method string getCustomOptions()
 * @method \Magento\GiftRegistry\Model\Item setCustomOptions(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Framework\Model\AbstractModel implements
    \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\GiftRegistry\Model\Item\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

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
     * @var array|\Magento\Catalog\Model\Resource\Url
     */
    protected $resourceUrl = array();

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Item\OptionFactory $optionFactory
     * @param \Magento\Catalog\Model\Resource\Url $resourceUrl
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GiftRegistry\Model\Item\OptionFactory $optionFactory,
        \Magento\Catalog\Model\Resource\Url $resourceUrl,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productFactory = $productFactory;
        $this->optionFactory = $optionFactory;
        $this->optionFactory = $optionFactory;
        $this->resourceUrl = $resourceUrl;
        $this->messageManager = $messageManager;
    }

    /**
     * Flag stating that options were successfully saved
     *
     * @var bool
     */
    protected $_flagOptionsSaved = null;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Resource\Item');
    }

    /**
     * Load item by registry id and product id
     *
     * @param int $registryId
     * @param int $productId
     * @return $this
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
     * @param \Magento\Checkout\Model\Cart $cart
     * @param int $qty
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     */
    public function addToCart(\Magento\Checkout\Model\Cart $cart, $qty)
    {
        $product = $this->_getProduct();
        $storeId = $this->getStoreId();

        if ($this->getQty() < $qty + $this->getQtyFulfilled()) {
            $qty = $this->getQty() - $this->getQtyFulfilled();
            $this->messageManager->addNotice(__('The quantity of "%s" product added to cart exceeds the quantity desired by the Gift Registry owner. The quantity added has been adjusted to meet remaining quantity %s.', $product->getName(), $qty));
        }

        $productIdsInCart = $cart->getProductIds();
        if (in_array($product->getId(), $productIdsInCart)) {
            foreach ($cart->getQuote()->getAllItems() as $item) {
                if (($item->getProduct()->getId() == $product->getId())
                    && ($item->getGiftregistryItemId() == $this->getId())
                    && (($item->getQty() + $qty) > ($this->getQty() - $this->getQtyFulfilled()))) {
                        $cart->removeItem($item->getId());
                        $this->messageManager->addNotice(__('Existing quantity of "%s" product in the cart has been replaced with quantity %s just requested.', $product->getName(), $qty));
                }
            }
        }

        if ($product->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
            return false;
        }

        if (!$product->isVisibleInSiteVisibility()) {
            if ($product->getStoreId() == $storeId) {
                return false;
            }
            $urlData = $this->resourceUrl->getRewriteByProductStore(array($product->getId() => $storeId));
            if (!isset($urlData[$product->getId()])) {
                return false;
            }
            $product->setUrlDataObject(new \Magento\Framework\Object($urlData));
            $visibility = $product->getUrlDataObject()->getVisibility();
            if (!in_array($visibility, $product->getVisibleInSiteVisibilities())) {
                return false;
            }
        }

        if (!$product->isSalable()) {
            throw new \Magento\Framework\Model\Exception(__('This product(s) is out of stock.'));
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
     * @param   \Magento\Catalog\Model\Product $product
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
        $skipOptions = array('qty', 'info_buyRequest');
        foreach ($options1 as $option) {
            $code = $option->getCode();
            if (in_array($code, $skipOptions)) {
                continue;
            }
            if (!isset(
                $options2[$code]
            ) || $options2[$code]->getValue() === null || $options2[$code]->getValue() != $option->getValue()
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set product attributes to item
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
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
     * @return \Magento\Catalog\Model\Product
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _getProduct()
    {
        if (!$this->_getData('product')) {
            $product = $this->productFactory->create()->load($this->getProductId());
            if (!$product->getId()) {
                throw new \Magento\Framework\Model\Exception(__('Please correct the product for adding the item to the quote.'));
            }
            $this->setProduct($product);
        }
        return $this->_getData('product');
    }

    /**
     * Return item product
     *
     * @return \Magento\Catalog\Model\Product
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
     * @return $this
     */
    protected function _afterSave()
    {
        $this->_saveItemOptions();
        return parent::_afterSave();
    }

    /**
     * Save item options
     *
     * @return $this
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

        $this->_flagOptionsSaved = true;
        // Report to watchers that options were saved

        return $this;
    }

    /**
     * Save model plus its options
     * Ensures saving options in case when resource model was not changed
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @param \Magento\GiftRegistry\Model\Item\Option $option
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function addOption($option)
    {
        if (is_array($option)) {
            $option = $this->optionFactory->create()->setData($option)->setItem($this);
        } elseif ($option instanceof \Magento\Sales\Model\Quote\Item\Option) {
            // import data from existing quote item option
            $option = $this->optionFactory->create()->setProduct(
                $option->getProduct()
            )->setCode(
                $option->getCode()
            )->setValue(
                $option->getValue()
            )->setItem(
                $this
            );
        } elseif ($option instanceof \Magento\Framework\Object && !$option instanceof \Magento\GiftRegistry\Model\Item\Option) {
            $option = $this->optionFactory->create()->setData(
                $option->getData()
            )->setProduct(
                $option->getProduct()
            )->setItem(
                $this
            );
        } elseif ($option instanceof \Magento\GiftRegistry\Model\Item\Option) {
            $option->setItem($this);
        } else {
            throw new \Magento\Framework\Model\Exception(__('Please correct the item option format.'));
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
     * @param   \Magento\GiftRegistry\Model\Item\Option $option
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _addOptionCode($option)
    {
        if (!isset($this->_optionsByCode[$option->getCode()])) {
            $this->_optionsByCode[$option->getCode()] = $option;
        } else {
            throw new \Magento\Framework\Model\Exception(__('An item option with code %1 already exists.', $option->getCode()));
        }
        return $this;
    }

    /**
     * Retrieve item option by code
     *
     * @param   string $code
     * @return  \Magento\GiftRegistry\Model\Item\Option|null
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
     * @return \Magento\Framework\Object
     */
    public function getBuyRequest()
    {
        $option = $this->getOptionByCode('info_buyRequest');
        $buyRequest = new \Magento\Framework\Object($option ? unserialize($option->getValue()) : null);
        $buyRequest->setOriginalQty($buyRequest->getQty())->setQty($this->getQty() * 1);
        // Qty value that is stored in buyRequest can be out-of-date
        return $buyRequest;
    }

    /**
     * Clone gift registry item
     *
     * @return $this
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
     * Needed to implement \Magento\Catalog\Model\Product\Configuration\Item\Interface.
     * Currently returns null, as far as we don't show file options and don't need controllers to give file.
     *
     * @return null|\Magento\Framework\Object
     */
    public function getFileDownloadParams()
    {
        return null;
    }

    /**
     * Validates and sets quantity for the related product
     *
     * @param int|float $quantity New item quantity
     * @return $this
     */
    public function setQty($quantity)
    {
        $quantity = (double)$quantity;

        if (!$this->_getProduct()->getTypeInstance()->canUseQtyDecimals()) {
            $quantity = round($quantity);
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        return $this->setData('qty', $quantity);
    }
}
