<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block;

/**
 * Customer Shared Wishlist Rss Block
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wishlist extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Customer instance
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_rss';

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Catalog\Helper\Output $outputHelper,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_outputHelper = $outputHelper;
        $this->_coreData = $coreData;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_customerFactory = $customerFactory;
        $this->_rssFactory = $rssFactory;
        parent::__construct($context, $httpContext, $productFactory, $data, $priceBlockTypes);
    }

    /**
     * Retrieve Wishlist model
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = $this->_wishlistFactory->create();
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
                if ($this->_wishlist->getCustomerId() != $this->_getCustomer()->getId()) {
                    $this->_wishlist->unsetData();
                }
            } else {
                if ($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = $this->_customerFactory->create();

            $params = $this->_coreData->urlDecode($this->getRequest()->getParam('data'));
            $data = explode(',', $params);
            $cId = abs(intval($data[0]));
            if ($cId && $cId == $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
                $this->_customer->load($cId);
            }
        }

        return $this->_customer;
    }

    /**
     * Build wishlist rss feed title
     *
     * @return string
     */
    protected function _getTitle()
    {
        return __('%1\'s Wishlist', $this->_getCustomer()->getName());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $this->_rssFactory->create();
        if ($this->_getWishlist()->getId()) {
            $newUrl = $this->_urlBuilder->getUrl(
                'wishlist/shared/index',
                array('code' => $this->_getWishlist()->getSharingCode())
            );
            $title = $this->_getTitle();
            $lang = $this->_storeConfig->getValue(
                'general/locale/code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $rssObj->_addHeader(
                array(
                    'title' => $title,
                    'description' => $title,
                    'link' => $newUrl,
                    'charset' => 'UTF-8',
                    'language' => $lang
                )
            );

            /** @var $wishlistItem \Magento\Wishlist\Model\Item*/
            foreach ($this->getWishlistItems() as $wishlistItem) {
                /* @var $product \Magento\Catalog\Model\Product */
                $product = $wishlistItem->getProduct();
                $productUrl = $this->getProductUrl($product);
                $product->setAllowedInRss(true);
                $product->setAllowedPriceInRss(true);
                $product->setProductUrl($productUrl);
                $args = array('product' => $product);

                $this->_eventManager->dispatch('rss_wishlist_xml_callback', $args);

                if (!$product->getAllowedInRss()) {
                    continue;
                }

                /** @var $outputHelper \Magento\Catalog\Helper\Output */
                $outputHelper = $this->_outputHelper;
                $description = '<table><tr><td><a href="' . $productUrl . '"><img src="' . $this->_imageHelper->init(
                    $product,
                    'thumbnail'
                )->resize(
                    75,
                    75
                ) .
                    '" border="0" align="left" height="75" width="75"></a></td>' .
                    '<td style="text-decoration:none;">' .
                    $outputHelper->productAttribute(
                    $product,
                    $product->getShortDescription(),
                    'short_description'
                ) . '<p>';

                if ($product->getAllowedPriceInRss()) {
                    $description .= $this->getPriceHtml($product, true);
                }
                $description .= '</p>';

                if ($this->hasDescription($product)) {
                    $description .= '<p>' . __(
                        'Comment:'
                    ) . ' ' . $outputHelper->productAttribute(
                        $product,
                        $product->getDescription(),
                        'description'
                    ) . '<p>';
                }
                $description .= '</td></tr></table>';
                $rssObj->_addEntry(
                    array(
                        'title' => $outputHelper->productAttribute($product, $product->getName(), 'name'),
                        'link' => $productUrl,
                        'description' => $description
                    )
                );
            }
        } else {
            $rssObj->_addHeader(
                array(
                    'title' => __('We cannot retrieve the wish list.'),
                    'description' => __('We cannot retrieve the wish list.'),
                    'link' => $this->_urlBuilder->getUrl(),
                    'charset' => 'UTF-8'
                )
            );
        }

        return $rssObj->createRssXml();
    }

    /**
     * Retrieve Product View URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        $additional['_rss'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Adding customized price template for product type, used as action in layouts
     *
     * @param string $type Catalog Product Type
     * @param string $block Block Type
     * @param string $template Template
     * @return void
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array('block' => $block, 'template' => $template);
        }
    }
}
