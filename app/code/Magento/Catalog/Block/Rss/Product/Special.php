<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Rss\Product;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Rss\DataProviderInterface;

/**
 * Class Special
 * @package Magento\Catalog\Block\Rss\Product
 */
class Special extends \Magento\Framework\View\Element\AbstractBlock implements DataProviderInterface
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $outputHelper;

    /**
     * @var \Magento\Catalog\Model\Rss\Product\Special
     */
    protected $rssModel;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface
     */
    protected $rssUrlBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\Rss\Product\Special $rssModel
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Rss\Product\Special $rssModel,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        array $data = array()
    ) {
        $this->outputHelper = $outputHelper;
        $this->imageHelper = $imageHelper;
        $this->rssModel = $rssModel;
        $this->rssUrlBuilder = $rssUrlBuilder;
        $this->priceCurrency = $priceCurrency;
        $this->catalogHelper = $catalogHelper;
        $this->httpContext = $httpContext;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setCacheKey('rss_catalog_special_' . $this->getStoreId() . '_' . $this->getCustomerGroupId());
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getRssData()
    {
        $newUrl = $this->rssUrlBuilder->getUrl(array('type' => 'special_products', 'store_id' => $this->getStoreId()));
        $title = __('%1 - Special Products', $this->storeManager->getStore()->getFrontendName());
        $lang = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $data = array(
            'title' => $title,
            'description' => $title,
            'link' => $newUrl,
            'charset' => 'UTF-8',
            'language' => $lang
        );

        $currentDate = new \Magento\Framework\Stdlib\DateTime\Date();
        foreach ($this->rssModel->getProductsCollection($this->getStoreId(), $this->getCustomerGroupId()) as $item) {
            /** @var $item \Magento\Catalog\Model\Product */
            $item->setAllowedInRss(true);
            $item->setAllowedPriceInRss(true);

            $this->_eventManager->dispatch('rss_catalog_special_xml_callback', array(
                'row' => $item->getData(),
                'product' => $item
            ));

            if (!$item->getAllowedInRss()) {
                continue;
            }

            $item->setUseSpecial(false);
            if ($item->getSpecialToDate() && $item->getFinalPrice() <= $item->getSpecialPrice() &&
                $item->getAllowedPriceInRss()
            ) {
                $compareDate = $currentDate->compareDate(
                    $item->getSpecialToDate(),
                    \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT
                );
                if (-1 === $compareDate || 0 === $compareDate) {
                    $item->setUseSpecial(true);
                }
            }
            $data['entries'][] = $this->getEntryData($item);
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $item
     * @return array
     */
    protected function getEntryData(\Magento\Catalog\Model\Product $item)
    {
        $description = '
            <table><tr>
                <td><a href="%s"><img src="%s" alt="" border="0" align="left" height="75" width="75" /></a></td>
                <td style="text-decoration:none;">%s %s</td>
            </tr></table>
        ';

        $specialPrice = '';
        if ($item->getAllowedPriceInRss()) {
            if ($this->catalogHelper->canApplyMsrp($item)) {
                $specialPrice = '<br/><a href="' . $item->getProductUrl() . '">' . __('Click for price') . '</a>';
            } else {
                $special = '';
                if ($item->getUseSpecial()) {
                    $special = '<br />' . __('Special Expires On: %1', $this->formatDate(
                        $item->getSpecialToDate(),
                        \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
                    ));
                }
                $specialPrice = sprintf(
                    '<p>%s %s%s</p>',
                    __('Price: %1', $this->priceCurrency->convertAndFormat($item->getPrice())),
                    __('Special Price: %1', $this->priceCurrency->convertAndFormat($item->getFinalPrice())),
                    $special
                );
            }
        }
        $description = sprintf(
            $description,
            $item->getProductUrl(),
            $this->imageHelper->init($item, 'thumbnail')->resize(75, 75),
            $this->outputHelper->productAttribute($item, $item->getDescription(), 'description'),
            $specialPrice
        );

        return array(
            'title' => $item->getName(),
            'link' => $item->getProductUrl(),
            'description' => $description
        );
    }

    /**
     * Get store id
     *
     * @return int
     */
    protected function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Get customer group id
     *
     * @return int
     */
    protected function getCustomerGroupId()
    {
        $customerGroupId =   (int) $this->getRequest()->getParam('cid');
        if ($customerGroupId == null) {
            $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        }
        return $customerGroupId;
    }

    /**
     * Check if RSS feed allowed
     *
     * @return mixed
     */
    public function isAllowed()
    {
        return $this->_scopeConfig->isSetFlag('rss/catalog/special', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifetime()
    {
        return 600;
    }

    /**
     * @return array
     */
    public function getFeeds()
    {
        $data = array();
        if ($this->isAllowed()) {
            $url = $this->rssUrlBuilder->getUrl(array('type' => 'special_products'));
            $data = array('label' => __('Special Products'), 'link' => $url);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthRequired()
    {
        return false;
    }
}
