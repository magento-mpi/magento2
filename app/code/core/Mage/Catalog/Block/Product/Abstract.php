<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Abstract Block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Product_Abstract extends Mage_Core_Block_Template
{
    protected $_priceBlock = array();

    /**
     * Default price block
     *
     * @var string
     */
    protected $_block = 'Mage_Catalog_Block_Product_Price';

    protected $_priceBlockDefaultTemplate = 'product/price.phtml';

    protected $_tierPriceDefaultTemplate  = 'product/view/tierprices.phtml';

    protected $_priceBlockTypes = array();

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    protected $_reviewsHelperBlock;

    /**
     * Default product amount per row
     *
     * @var int
     */
    protected $_defaultColumnCount = 3;

    /**
     * Product amount per row depending on custom page layout of category
     *
     * @var array
     */
    protected $_columnCountLayoutDepend = array();

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp';

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = array();
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }
        return $this->helper('Mage_Checkout_Helper_Cart')->getAddUrl($product, $additional);
    }

    /**
     * Retrieves url for form submitting:
     * some objects can use setSubmitRouteData() to set route and params for form submitting,
     * otherwise default url will be used
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getSubmitUrl($product, $additional = array())
    {
        $submitRouteData = $this->getData('submit_route_data');
        if ($submitRouteData) {
            $route = $submitRouteData['route'];
            $params = isset($submitRouteData['params']) ? $submitRouteData['params'] : array();
            $submitUrl = $this->getUrl($route, array_merge($params, $additional));
        } else {
            $submitUrl = $this->getAddToCartUrl($product, $additional);
        }
        return $submitUrl;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->helper('Mage_Wishlist_Helper_Data')->getAddUrl($product);
    }

    /**
     * Retrieve Add Product to Compare Products List URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCompareUrl($product)
    {
        return $this->helper('Mage_Catalog_Helper_Product_Compare')->getAddUrl($product);
    }

    /**
     * Gets minimal sales quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int|null
     */
    public function getMinimalQty($product)
    {
        $stockItem = $product->getStockItem();
        if ($stockItem) {
            return ($stockItem->getMinSaleQty()
                && $stockItem->getMinSaleQty() > 0 ? $stockItem->getMinSaleQty() * 1 : null);
        }
        return null;
    }

    protected function _getPriceBlock($productTypeId)
    {
        if (!isset($this->_priceBlock[$productTypeId])) {
            $block = $this->_block;
            if (isset($this->_priceBlockTypes[$productTypeId])) {
                if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$productTypeId]['block'];
                }
            }
            $this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
        }
        return $this->_priceBlock[$productTypeId];
    }

    protected function _getPriceBlockTemplate($productTypeId)
    {
        if (isset($this->_priceBlockTypes[$productTypeId])) {
            if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$productTypeId]['template'];
            }
        }
        return $this->_priceBlockDefaultTemplate;
    }


    /**
     * Prepares and returns block to render some product type
     *
     * @param string $productType
     * @return Mage_Core_Block_Template
     */
    public function _preparePriceRenderer($productType)
    {
        return $this->_getPriceBlock($productType)
            ->setTemplate($this->_getPriceBlockTemplate($productType))
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs);
    }

    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $product->getTypeId();
        if (Mage::helper('Mage_Catalog_Helper_Data')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
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
     * Adding customized price template for product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }

    /**
     * Get product reviews summary
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false,
        $displayIfNoReviews = false)
    {
        if ($this->_initReviewsHelperBlock()) {
            return $this->_reviewsHelperBlock->getSummaryHtml($product, $templateType, $displayIfNoReviews);
        }

        return '';
    }

    /**
     * Add/replace reviews summary template by type
     *
     * @param string $type
     * @param string $template
     * @return string
     */
    public function addReviewSummaryTemplate($type, $template)
    {
        if ($this->_initReviewsHelperBlock()) {
            $this->_reviewsHelperBlock->addTemplate($type, $template);
        }

        return '';
    }

    /**
     * Create reviews summary helper block once
     *
     * @return boolean
     */
    protected function _initReviewsHelperBlock()
    {
        if (!$this->_reviewsHelperBlock) {
            if (!Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Review')) {
                return false;
            } else {
                $this->_reviewsHelperBlock = $this->getLayout()->createBlock('Mage_Review_Block_Helper');
            }
        }

        return true;
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    public function getTierPriceTemplate()
    {
        if (!$this->hasData('tier_price_template')) {
            return $this->_tierPriceDefaultTemplate;
        }

        return $this->getData('tier_price_template');
    }
    /**
     * Returns product tier price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getTierPriceHtml($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $this->_getPriceBlock($product->getTypeId())
            ->setTemplate($this->getTierPriceTemplate())
            ->setProduct($product)
            ->setInGrouped($this->getProduct()->isGrouped())
            ->toHtml();
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices  = $product->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $_productPrice = $product->getPrice();
                if ($_productPrice != $product->getFinalPrice()) {
                    $_productPrice = $product->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $product->getGroupPrice();
                if ($_productPrice > $groupPrice) {
                    $_productPrice = $groupPrice;
                }

                if ($price['price'] < $_productPrice) {
                    $price['savePercent'] = ceil(100 - ((100 / $_productPrice) * $price['price']));

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('Mage_Tax_Helper_Data')->getPrice($product, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('Mage_Tax_Helper_Data')->getPrice($product, $price['website_price'], true)
                        )
                    );

                    if (Mage::helper('Mage_Catalog_Helper_Data')->canApplyMsrp($product)) {
                        $oldPrice = $product->getFinalPrice();
                        $product->setPriceCalculation(false);
                        $product->setPrice($tierPrice);
                        $product->setFinalPrice($tierPrice);

                        $this->getPriceHtml($product);
                        $product->setPriceCalculation(true);

                        $price['real_price_html'] = $product->getRealPriceHtml();
                        $product->setFinalPrice($oldPrice);
                    }

                    $res[] = $price;
                }
            }
        }

        return $res;
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addProductAttributesAndPrices(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes())
            ->addUrlRewrite();
    }

    /**
     * Retrieve given media attribute label or product name if no label
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $mediaAttributeCode
     *
     * @return string
     */
    public function getImageLabel($product=null, $mediaAttributeCode='image')
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }

        $label = $product->getData($mediaAttributeCode.'_label');
        if (empty($label)) {
            $label = $product->getName();
        }

        return $label;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional the route params
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    /**
     * Check Product has URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve product amount per row
     *
     * @return int
     */
    public function getColumnCount()
    {
        if (!$this->_getData('column_count')) {
            $pageLayout = $this->getPageLayout();
            if ($pageLayout && $this->getColumnCountLayoutDepend($pageLayout->getCode())) {
                $this->setData(
                    'column_count',
                    $this->getColumnCountLayoutDepend($pageLayout->getCode())
                );
            } else {
                $this->setData('column_count', $this->_defaultColumnCount);
            }
        }

        return (int) $this->_getData('column_count');
    }

    /**
     * Add row size depends on page layout
     *
     * @param string $pageLayout
     * @param int $columnCount
     * @return Mage_Catalog_Block_Product_List
     */
    public function addColumnCountLayoutDepend($pageLayout, $columnCount)
    {
        $this->_columnCountLayoutDepend[$pageLayout] = $columnCount;
        return $this;
    }

    /**
     * Remove row size depends on page layout
     *
     * @param string $pageLayout
     * @return Mage_Catalog_Block_Product_List
     */
    public function removeColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            unset($this->_columnCountLayoutDepend[$pageLayout]);
        }

        return $this;
    }

    /**
     * Retrieve row size depends on page layout
     *
     * @param string $pageLayout
     * @return int|boolean
     */
    public function getColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            return $this->_columnCountLayoutDepend[$pageLayout];
        }

        return false;
    }

    /**
     * Retrieve current page layout
     *
     * @return Varien_Object
     */
    public function getPageLayout()
    {
        return $this->helper('Mage_Page_Helper_Layout')->getCurrentPageLayout();
    }

    /**
     * Check whether the price can be shown for the specified product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function getCanShowProductPrice($product)
    {
        return $product->getCanShowPrice() !== false;
    }

    /**
     * Get if it is necessary to show product stock status
     *
     * @return bool
     */
    public function displayProductStockStatus()
    {
        $statusInfo = new Varien_Object(array('display_status' => true));
        Mage::dispatchEvent('catalog_block_product_status_display', array('status' => $statusInfo));
        return (boolean)$statusInfo->getDisplayStatus();
    }

    /**
     * If exists price template block, retrieve price blocks from it
     *
     * @return Mage_Catalog_Block_Product_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /* @var $block Mage_Catalog_Block_Product_Price_Template */
        $block = $this->getLayout()->getBlock('catalog_product_price_template');
        if ($block) {
            foreach ($block->getPriceBlockTypes() as $type => $priceBlock) {
                $this->addPriceBlockType($type, $priceBlock['block'], $priceBlock['template']);
            }
        }

        return $this;
    }

    /**
     * Product thumbnail image url getter
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getThumbnailUrl($product)
    {
        return (string)$this->helper('Mage_Catalog_Helper_Image')->init($product, 'thumbnail')
            ->resize($this->getThumbnailSize());
    }

    /**
     * Thumbnail image size getter
     *
     * @return int
     */
    public function getThumbnailSize()
    {
        return $this->getVar('product_thumbnail_image_size', 'Mage_Catalog');
    }

    /**
     * Product thumbnail image sidebar url getter
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getThumbnailSidebarUrl($product)
    {
        return (string) $this->helper('Mage_Catalog_Helper_Image')->init($product, 'thumbnail')
            ->resize($this->getThumbnailSidebarSize());
    }

    /**
     * Thumbnail image sidebar size getter
     *
     * @return int
     */
    public function getThumbnailSidebarSize()
    {
        return $this->getVar('product_thumbnail_image_sidebar_size', 'Mage_Catalog');
    }

    /**
     * Product small image url getter
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSmallImageUrl($product)
    {
        return (string) $this->helper('Mage_Catalog_Helper_Image')->init($product, 'small_image')
            ->resize($this->getSmallImageSize());
    }

    /**
     * Small image size getter
     *
     * @return int
     */
    public function getSmallImageSize()
    {
        return $this->getVar('product_small_image_size', 'Mage_Catalog');
    }

    /**
     * Product small image sidebar url getter
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSmallImageSidebarUrl($product)
    {
        return (string) $this->helper('Mage_Catalog_Helper_Image')->init($product, 'small_image')
            ->resize($this->getSmallImageSidebarSize());
    }

    /**
     * Small image sidebar size getter
     *
     * @return int
     */
    public function getSmallImageSidebarSize()
    {
        return $this->getVar('product_small_image_sidebar_size', 'Mage_Catalog');
    }

    /**
     * Product base image url getter
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getBaseImageUrl($product)
    {
        return (string)$this->helper('Mage_Catalog_Helper_Image')->init($product, 'image')
            ->resize($this->getBaseImageSize());
    }

    /**
     * Base image size getter
     *
     * @return int
     */
    public function getBaseImageSize()
    {
        return $this->getVar('product_base_image_size', 'Mage_Catalog');
    }

    /**
     * Product base image icon url getter
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getBaseImageIconUrl($product)
    {
        return (string)$this->helper('Mage_Catalog_Helper_Image')->init($product, 'image')
            ->resize($this->getBaseImageIconSize());
    }

    /**
     * Base image icon size getter
     *
     * @return int
     */
    public function getBaseImageIconSize()
    {
        return $this->getVar('product_base_image_icon_size', 'Mage_Catalog');
    }
}
