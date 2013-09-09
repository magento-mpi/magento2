<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist Product Items abstract Block
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Wishlist_Block_Abstract extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Wishlist Product Items Collection
     *
     * @var Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected $_collection;

    /**
     * Store wishlist Model
     *
     * @var Magento_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * List of block settings to render prices for different product types
     *
     * @var array
     */
    protected $_itemPriceBlockTypes = array();

    /**
     * List of block instances to render prices for different product types
     *
     * @var array
     */
    protected $_cachedItemPriceBlocks = array();

    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($eventManager, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addItemPriceBlockType(
            'default',
            'Magento_Wishlist_Block_Render_Item_Price',
            'render/item/price.phtml'
        );
    }

    /**
     * Retrieve Wishlist Data Helper
     *
     * @return Magento_Wishlist_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->_wishlistData;
    }

    /**
     * Retrieve Customer Session instance
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        return $this->_getHelper()->getWishlist();
    }

    /**
     * Prepare additional conditions to collection
     *
     * @param Magento_Wishlist_Model_Resource_Item_Collection $collection
     * @return Magento_Wishlist_Block_Customer_Wishlist
     */
    protected function _prepareCollection($collection)
    {
        return $this;
    }

    /**
     * Create wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->_getWishlist()->getItemCollection();
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlistItems()
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_createWishlistItemCollection();
            $this->_prepareCollection($this->_collection);
        }

        return $this->_collection;
    }

    /**
     * Retrieve wishlist instance
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getWishlistInstance()
    {
        return $this->_getWishlist();
    }

    /**
     * Retrieve URL for Removing item from wishlist
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     *
     * @return string
     */
    public function getItemRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }

    /**
     * Retrieve Add Item to shopping cart URL
     *
     * @param string|Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return string
     */
    public function getItemAddToCartUrl($item)
    {
        return $this->_getHelper()->getAddToCartUrl($item);
    }

    /**
     * Retrieve Add Item to shopping cart URL from shared wishlist
     *
     * @param string|Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return string
     */
    public function getSharedItemAddToCartUrl($item)
    {
        return $this->_getHelper()->getSharedAddToCartUrl($item);
    }

    /**
     * Retrieve URL for adding Product to wishlist
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->_getHelper()->getAddUrl($product);
    }

     /**
     * Returns item configure url in wishlist
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $product
     *
     * @return string
     */
    public function getItemConfigureUrl($product)
    {
        if ($product instanceof Magento_Catalog_Model_Product) {
            $id = $product->getWishlistItemId();
        } else {
            $id = $product->getId();
        }
        $params = array('id' => $id);

        return $this->getUrl('wishlist/index/configure/', $params);
    }


    /**
     * Retrieve Escaped Description for Wishlist Item
     *
     * @param Magento_Catalog_Model_Product $item
     * @return string
     */
    public function getEscapedDescription($item)
    {
        if ($item->getDescription()) {
            return $this->escapeHtml($item->getDescription());
        }
        return '&nbsp;';
    }

    /**
     * Check Wishlist item has description
     *
     * @param Magento_Catalog_Model_Product $item
     * @return bool
     */
    public function hasDescription($item)
    {
        return trim($item->getDescription()) != '';
    }

    /**
     * Retrieve formated Date
     *
     * @param string $date
     * @return string
     */
    public function getFormatedDate($date)
    {
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Check is the wishlist has a salable product(s)
     *
     * @return bool
     */
    public function isSaleable()
    {
        foreach ($this->getWishlistItems() as $item) {
            if ($item->getProduct()->isSaleable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve wishlist loaded items count
     *
     * @return int
     */
    public function getWishlistItemsCount()
    {
        return $this->_getWishlist()->getItemsCount();
    }

    /**
     * Retrieve Qty from item
     *
     * @param Magento_Wishlist_Model_Item|Magento_Catalog_Model_Product $item
     * @return float
     */
    public function getQty($item)
    {
        $qty = $item->getQty() * 1;
        if (!$qty) {
            $qty = 1;
        }
        return $qty;
    }

    /**
     * Check is the wishlist has items
     *
     * @return bool
     */
    public function hasWishlistItems()
    {
        return $this->getWishlistItemsCount() > 0;
    }

    /**
     * Adds special block to render price for item with specific product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addItemPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_itemPriceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }

    /**
     * Returns block to render item with some product type
     *
     * @param string $productType
     * @return Magento_Core_Block_Template
     */
    protected function _getItemPriceBlock($productType)
    {
        if (!isset($this->_itemPriceBlockTypes[$productType])) {
            $productType = 'default';
        }

        if (!isset($this->_cachedItemPriceBlocks[$productType])) {
            $blockType = $this->_itemPriceBlockTypes[$productType]['block'];
            $template = $this->_itemPriceBlockTypes[$productType]['template'];
            $block = $this->getLayout()->createBlock($blockType)
                ->setTemplate($template);
            $this->_cachedItemPriceBlocks[$productType] = $block;
        }
        return $this->_cachedItemPriceBlocks[$productType];
    }

    /**
     * Returns product price block html
     * Overwrites parent price html return to be ready to show configured, partially configured and
     * non-configured products
     *
     * @param Magento_Catalog_Model_Product $product
     * @param bool $displayMinimalPrice
     * @param string $idSuffix
     *
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $product->getTypeId();
        if ($this->_catalogData->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->setIsEmulateMode(true)
                ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($type_id)
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    /**
     * Retrieve URL to item Product
     *
     * @param  Magento_Wishlist_Model_Item|Magento_Catalog_Model_Product $item
     * @param  array $additional
     * @return string
     */
    public function getProductUrl($item, $additional = array())
    {
        if ($item instanceof Magento_Catalog_Model_Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $buyRequest = $item->getBuyRequest();
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = Mage::getModel('Magento_Catalog_Model_Product')
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->load($config['product_id']);
            }
        }
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Product image url getter
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        return (string)$this->helper('Magento_Catalog_Helper_Image')->init($product, 'small_image')
            ->resize($this->getImageSize());
    }

    /**
     * Product image size getter
     *
     * @return int
     */
    public function getImageSize()
    {
        return $this->getVar('product_image_size', 'Magento_Wishlist');
    }
}
