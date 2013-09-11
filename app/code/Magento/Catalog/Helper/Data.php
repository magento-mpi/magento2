<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog data helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const PRICE_SCOPE_GLOBAL               = 0;
    const PRICE_SCOPE_WEBSITE              = 1;
    const XML_PATH_PRICE_SCOPE             = 'catalog/price/scope';
    const XML_PATH_SEO_SAVE_HISTORY        = 'catalog/seo/save_rewrites_history';
    const CONFIG_USE_STATIC_URLS           = 'cms/wysiwyg/use_static_urls_in_catalog';
    const CONFIG_PARSE_URL_DIRECTIVES      = 'catalog/frontend/parse_url_directives';
    const XML_PATH_CONTENT_TEMPLATE_FILTER = 'global/catalog/content/tempate_filter';
    const XML_PATH_DISPLAY_PRODUCT_COUNT   = 'catalog/layered_navigation/display_product_count';

    /**
     * Minimum advertise price constants
     */
    const XML_PATH_MSRP_ENABLED = 'sales/msrp/enabled';
    const XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'sales/msrp/display_price_type';
    const XML_PATH_MSRP_APPLY_TO_ALL = 'sales/msrp/apply_for_all';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE = 'sales/msrp/explanation_message';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS = 'sales/msrp/explanation_message_whats_this';


    /**
     * Breadcrumb Path cache
     *
     * @var string
     */
    protected $_categoryPath;

    /**
     * Array of product types that MAP enabled
     *
     * @var array
     */
    protected $_mapApplyToProductType = null;

    /**
     * Currenty selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return \Magento\Catalog\Helper\Data
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Return current category path or get it from current category
     * and creating array of categories|product paths for breadcrumbs
     *
     * @return string
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {

            $path = array();
            if ($category = $this->getCategory()) {
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $path['category'.$categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link' => $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : ''
                        );
                    }
                }
            }

            if ($this->getProduct()) {
                $path['product'] = array('label'=>$this->getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }

    /**
     * Check is category link
     *
     * @param int $categoryId
     * @return bool
     */
    protected function _isCategoryLink($categoryId)
    {
        if ($this->getProduct()) {
            return true;
        }
        if ($categoryId != $this->getCategory()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Return current category object
     *
     * @return \Magento\Catalog\Model\Category|null
     */
    public function getCategory()
    {
        return \Mage::registry('current_category');
    }

    /**
     * Retrieve current Product object
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return \Mage::registry('current_product');
    }

    /**
     * Retrieve Visitor/Customer Last Viewed URL
     *
     * @return string
     */
    public function getLastViewedUrl()
    {
        if ($productId = \Mage::getSingleton('Magento\Catalog\Model\Session')->getLastViewedProductId()) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
            /* @var $product \Magento\Catalog\Model\Product */
            if (\Mage::helper('Magento\Catalog\Helper\Product')->canShow($product, 'catalog')) {
                return $product->getProductUrl();
            }
        }
        if ($categoryId = \Mage::getSingleton('Magento\Catalog\Model\Session')->getLastViewedCategoryId()) {
            $category = \Mage::getModel('Magento\Catalog\Model\Category')->load($categoryId);
            /* @var $category \Magento\Catalog\Model\Category */
            if (!\Mage::helper('Magento\Catalog\Helper\Category')->canShow($category)) {
                return '';
            }
            return $category->getCategoryUrl();
        }
        return '';
    }

    /**
     * Split SKU of an item by dashes and spaces
     * Words will not be broken, unless thir length is greater than $length
     *
     * @param string $sku
     * @param int $length
     * @return array
     */
    public function splitSku($sku, $length = 30)
    {
        return \Mage::helper('Magento\Core\Helper\String')->str_split($sku, $length, true, false, '[\-\s]');
    }

    /**
     * Retrieve attribute hidden fields
     *
     * @return array
     */
    public function getAttributeHiddenFields()
    {
        if (\Mage::registry('attribute_type_hidden_fields')) {
            return \Mage::registry('attribute_type_hidden_fields');
        } else {
            return array();
        }
    }

    /**
     * Retrieve attribute disabled types
     *
     * @return array
     */
    public function getAttributeDisabledTypes()
    {
        if (\Mage::registry('attribute_type_disabled_types')) {
            return \Mage::registry('attribute_type_disabled_types');
        } else {
            return array();
        }
    }

    /**
     * Retrieve Catalog Price Scope
     *
     * @return int
     */
    public function getPriceScope()
    {
        return \Mage::getStoreConfig(self::XML_PATH_PRICE_SCOPE);
    }

    /**
     * Is Global Price
     *
     * @return bool
     */
    public function isPriceGlobal()
    {
        return $this->getPriceScope() == self::PRICE_SCOPE_GLOBAL;
    }

    /**
     * Indicate whether to save URL Rewrite History or not (create redirects to old URLs)
     *
     * @param int $storeId Store View
     * @return bool
     */
    public function shouldSaveUrlRewritesHistory($storeId = null)
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_SEO_SAVE_HISTORY, $storeId);
    }

    /**
     * Check if the store is configured to use static URLs for media
     *
     * @return bool
     */
    public function isUsingStaticUrlsAllowed()
    {
        return \Mage::getStoreConfigFlag(self::CONFIG_USE_STATIC_URLS, $this->_storeId);
    }

    /**
     * Check if the parsing of URL directives is allowed for the catalog
     *
     * @return bool
     */
    public function isUrlDirectivesParsingAllowed()
    {
        return \Mage::getStoreConfigFlag(self::CONFIG_PARSE_URL_DIRECTIVES, $this->_storeId);
    }

    /**
     * Retrieve template processor for catalog content
     *
     * @return \Magento\Filter\Template
     */
    public function getPageTemplateProcessor()
    {
        $model = (string)\Mage::getConfig()->getNode(self::XML_PATH_CONTENT_TEMPLATE_FILTER);
        return \Mage::getModel($model);
    }

    /**
     * Check if Minimum Advertised Price is enabled
     *
     * @return bool
     */
    public function isMsrpEnabled()
    {
        return (bool)\Mage::getStoreConfig(self::XML_PATH_MSRP_ENABLED, $this->_storeId);
    }

    /**
     * Return MAP display actual type
     *
     * @return null|string
     */
    public function getMsrpDisplayActualPriceType()
    {
        return \Mage::getStoreConfig(self::XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE, $this->_storeId);
    }

    /**
     * Check if MAP apply to all products
     *
     * @return bool
     */
    public function isMsrpApplyToAll()
    {
        return (bool)\Mage::getStoreConfig(self::XML_PATH_MSRP_APPLY_TO_ALL, $this->_storeId);
    }

    /**
     * Return MAP explanation message
     *
     * @return string
     */
    public function getMsrpExplanationMessage()
    {
        return $this->escapeHtml(
            \Mage::getStoreConfig(self::XML_PATH_MSRP_EXPLANATION_MESSAGE, $this->_storeId),
            array('b','br','strong','i','u', 'p', 'span')
        );
    }

    /**
     * Return MAP explanation message for "Whats This" window
     *
     * @return string
     */
    public function getMsrpExplanationMessageWhatsThis()
    {
        return $this->escapeHtml(
            \Mage::getStoreConfig(self::XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS, $this->_storeId),
            array('b','br','strong','i','u', 'p', 'span')
        );
    }

    /**
     * Check if can apply Minimum Advertise price to product
     * in specific visibility
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @param int $visibility Check displaying price in concrete place (by default generally)
     * @param bool $checkAssociatedItems
     * @return bool
     */
    public function canApplyMsrp($product, $visibility = null, $checkAssociatedItems = true)
    {
        if (!$this->isMsrpEnabled()) {
            return false;
        }

        if (is_numeric($product)) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')
                ->setStoreId(\Mage::app()->getStore()->getId())
                ->load($product);
        }

        if (!$this->canApplyMsrpToProductType($product)) {
            return false;
        }

        $result = $product->getMsrpEnabled();
        if ($result == \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG) {
            $result = $this->isMsrpApplyToAll();
        }

        if (!$product->hasMsrpEnabled() && $this->isMsrpApplyToAll()) {
            $result = true;
        }

        if ($result && $visibility !== null) {
            $productVisibility = $product->getMsrpDisplayActualPriceType();
            if ($productVisibility == \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG) {
                $productVisibility = $this->getMsrpDisplayActualPriceType();
            }
            $result = ($productVisibility == $visibility);
        }

        if ($product->getTypeInstance()->isComposite($product)
            && $checkAssociatedItems
            && (!$result || $visibility !== null)
        ) {
            $resultInOptions = $product->getTypeInstance()->isMapEnabledInOptions($product, $visibility);
            if ($resultInOptions !== null) {
                $result = $resultInOptions;
            }
        }

        return $result;
    }

    /**
     * Check whether MAP applied to product Product Type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function canApplyMsrpToProductType($product)
    {
        if($this->_mapApplyToProductType === null) {
            /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            $attribute = \Mage::getModel('Magento\Catalog\Model\Resource\Eav\Attribute')
                ->loadByCode(\Magento\Catalog\Model\Product::ENTITY, 'msrp_enabled');
            $this->_mapApplyToProductType = $attribute->getApplyTo();
        }
        return empty($this->_mapApplyToProductType) || in_array($product->getTypeId(), $this->_mapApplyToProductType);
    }

    /**
     * Get MAP message for price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getMsrpPriceMessage($product)
    {
        $message = "";
        if ($this->canApplyMsrp(
            $product,
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type::TYPE_IN_CART
        )) {
            $message = __('To see product price, add this item to your cart. You can always remove it later.');
        } elseif ($this->canApplyMsrp(
            $product,
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type::TYPE_BEFORE_ORDER_CONFIRM
        )) {
            $message = __('See price before order confirmation.');
        }
        return $message;
    }

    /**
     * Check is product need gesture to show price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isShowPriceOnGesture($product)
    {
        return $this->canApplyMsrp(
            $product,
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type::TYPE_ON_GESTURE
        );
    }

    /**
     * Whether to display items count for each filter option
     * @param int $storeId Store view ID
     * @return bool
     */
    public function shouldDisplayProductCountOnLayer($storeId = null)
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_PRODUCT_COUNT, $storeId);
    }
}
