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
 * Product View block
 *
 * @category Magento
 * @package  Magento_Catalog
 * @module   Catalog
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_View extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Helper_String $coreString,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_coreString = $coreString;
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Add meta information from product to head block
     *
     * @return Magento_Catalog_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento_Catalog_Block_Breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            $title = $product->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }
            $keyword = $product->getMetaKeyword();
            $currentCategory = $this->_coreRegistry->registry('current_category');
            if ($keyword) {
                $headBlock->setKeywords($keyword);
            } elseif($currentCategory) {
                $headBlock->setKeywords($product->getName());
            }
            $description = $product->getMetaDescription();
            if ($description) {
                $headBlock->setDescription( ($description) );
            } else {
                $headBlock->setDescription($this->_coreString->substr($product->getDescription(), 0, 255));
            }
            //@todo: move canonical link to separate block
            if ($this->helper('Magento_Catalog_Helper_Product')->canUseCanonicalTag()
                && !$headBlock->getChildBlock('magento-page-head-product-canonical-link')
            ) {
                $params = array('_ignore_category'=>true);
                $headBlock->addChild(
                    'magento-page-head-product-canonical-link',
                    'Magento_Page_Block_Html_Head_Link',
                    array(
                        'url' => $product->getUrlModel()->getUrl($product, $params),
                        'properties' => array('attributes' => array('rel' => 'canonical'))
                    )
                );
            }
        }
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->getProduct()->getName());
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve current product model
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->load($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = $this->_coreRegistry->registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = $this->_coreData->urlEncode($addUrlValue);

        return $this->helper('Magento_Checkout_Helper_Cart')->getAddUrl($product, $additional);
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        if (!$this->hasOptions()) {
            return $this->_coreData->jsonEncode($config);
        }

        $_request = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRateRequest(false, false, false);
        /* @var $product Magento_Catalog_Model_Product */
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRate($_request);

        $_request = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        $_priceInclTax = $this->_taxData->getPrice($product, $_finalPrice, true);
        $_priceExclTax = $this->_taxData->getPrice($product, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = $this->_coreData->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = $this->_coreData->currency(
                $this->_taxData->getPrice($product, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'productId'           => $product->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => $this->_taxData->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => $this->_taxData->displayPriceIncludingTax(),
            'showBothPrices'      => $this->_taxData->displayBothPrices(),
            'productPrice'        => $this->_coreData->currency($_finalPrice, false, false),
            'productOldPrice'     => $this->_coreData->currency($_regularPrice, false, false),
            'priceInclTax'        => $this->_coreData->currency($_priceInclTax, false, false),
            'priceExclTax'        => $this->_coreData->currency($_priceExclTax, false, false),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
        );

        $responseObject = new Magento_Object();
        $this->_eventManager->dispatch('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }

        return $this->_coreData->jsonEncode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     */
    public function isStartCustomization()
    {
        return $this->getProduct()->getConfigureMode() || Mage::app()->getRequest()->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|Magento_Catalog_Model_Product $product
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Get container name, where product options should be displayed
     *
     * @return string
     */
    public function getOptionsContainer()
    {
        return $this->getProduct()->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
    }
}
