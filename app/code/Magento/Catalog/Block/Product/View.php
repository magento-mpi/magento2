<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product;

/**
 * Product View block
 */
class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * Tax calculation
     *
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculation;

    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\Product\Compare $compareProduct
     * @param \Magento\Theme\Helper\Layout $layoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareProduct,
        \Magento\Theme\Helper\Layout $layoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Stdlib\String $string,
        \Magento\Catalog\Helper\Product $productHelper,
        array $data = array()
    ) {
        $this->_productHelper = $productHelper;
        $this->_coreData = $coreData;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_productFactory = $productFactory;
        $this->_taxCalculation = $taxCalculation;
        $this->string = $string;
        parent::__construct(
            $context,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $cartHelper,
            $wishlistHelper,
            $compareProduct,
            $layoutHelper,
            $imageHelper,
            $data
        );
    }

    /**
     * Add meta information from product to head block
     *
     * @return \Magento\Catalog\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');
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
                $headBlock->setDescription($this->string->substr($product->getDescription(), 0, 255));
            }
            //@todo: move canonical link to separate block
            if ($this->_productHelper->canUseCanonicalTag()
                && !$headBlock->getChildBlock('magento-page-head-product-canonical-link')
            ) {
                $params = array('_ignore_category'=>true);
                $headBlock->addChild(
                    'magento-page-head-product-canonical-link',
                    'Magento\Theme\Block\Html\Head\Link',
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
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->_productFactory->create()->load($this->getProductId());
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
     * @param \Magento\Catalog\Model\Product $product
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

        $addUrlKey = \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = $this->_urlBuilder->getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = $this->_coreData->urlEncode($addUrlValue);

        return $this->_cartHelper->getAddUrl($product, $additional);
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
            return $this->_jsonEncoder->encode($config);
        }

        $_request = $this->_taxCalculation->getRateRequest(false, false, false);
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = $this->_taxCalculation->getRate($_request);

        $_request = $this->_taxCalculation->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = $this->_taxCalculation->getRate($_request);

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
            'priceFormat'         => $this->_locale->getJsPriceFormat(),
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

        $responseObject = new \Magento\Object();
        $this->_eventManager->dispatch('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
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
        return $this->getProduct()->getConfigureMode() || $this->_request->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|\Magento\Catalog\Model\Product $product
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
