<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block\Catalog;

/**
 * Review form block
 */
class Special extends \Magento\Rss\Block\Catalog\AbstractCatalog
{
    /**
     * \Magento\Framework\Stdlib\DateTime\DateInterface object for date comparsions
     *
     * @var \Magento\Framework\Stdlib\DateTime\Date
     */
    protected static $_currentDate = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\Framework\Model\Resource\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $_outputHelper;

    /** @var \Magento\Msrp\Helper\Data */
    protected $msrpData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Framework\Model\Resource\Iterator $resourceIterator
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Msrp\Helper\Data $msrpData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Framework\Model\Resource\Iterator $resourceIterator,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Msrp\Helper\Data $msrpData,
        array $data = array()
    ) {
        $this->_outputHelper = $outputHelper;
        $this->_imageHelper = $imageHelper;
        $this->_priceCurrency = $priceCurrency;
        $this->_productFactory = $productFactory;
        $this->_rssFactory = $rssFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->msrpData = $msrpData;
        parent::__construct($context, $httpContext, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        /*
         * setting cache to save the rss for 10 minutes
         */
        $this->setCacheKey('rss_catalog_special_' . $this->_getStoreId() . '_' . $this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();

        //customer group id
        $customerGroupId = $this->_getCustomerGroupId();

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();
        $product->setStoreId($storeId);
        $specials = $product->getResourceCollection()->addPriceDataFieldFilter(
            '%s < %s',
            array('final_price', 'price')
        )->addPriceData(
            $customerGroupId,
            $websiteId
        )->addAttributeToSelect(
            array(
                'name',
                'short_description',
                'description',
                'price',
                'thumbnail',
                'special_price',
                'special_to_date',
                'msrp',
                'msrp_display_actual_price_type',
            ),
            'left'
        )->addAttributeToSort(
            'name',
            'asc'
        );

        $newUrl = $this->_urlBuilder->getUrl('rss/catalog/special/store_id/' . $storeId);
        $title = __('%1 - Special Products', $this->_storeManager->getStore()->getFrontendName());
        $lang = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(
            array(
                'title' => $title,
                'description' => $title,
                'link' => $newUrl,
                'charset' => 'UTF-8',
                'language' => $lang
            )
        );

        $results = array();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        $this->_resourceIterator->walk(
            $specials->getSelect(),
            array(array($this, 'addSpecialXmlCallback')),
            array('rssObj' => $rssObj, 'results' => &$results)
        );

        if (sizeof($results) > 0) {
            foreach ($results as $result) {
                // render a row for RSS feed
                $product->setData($result);
                $html = sprintf(
                    '<table><tr>' .
                    '<td><a href="%s"><img src="%s" alt="" border="0" align="left" height="75" width="75" /></a></td>' .
                    '<td style="text-decoration:none;">%s',
                    $product->getProductUrl(),
                    $this->_imageHelper->init($product, 'thumbnail')->resize(75, 75),
                    $this->_outputHelper->productAttribute($product, $product->getDescription(), 'description')
                );

                // add price data if needed
                if ($product->getAllowedPriceInRss()) {
                    if ($this->msrpData->canApplyMsrp($product)) {
                        $html .= '<br/><a href="' . $product->getProductUrl() . '">' . __('Click for price') . '</a>';
                    } else {
                        $special = '';
                        if ($result['use_special']) {
                            $special = '<br />' . __(
                                'Special Expires On: %1',
                                $this->formatDate(
                                    $result['special_to_date'],
                                    \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
                                )
                            );
                        }
                        $html .= sprintf(
                            '<p>%s %s%s</p>',
                            __('Price: %1', $this->_priceCurrency->convertAndFormat($result['price'])),
                            __('Special Price: %1', $this->_priceCurrency->convertAndFormat($result['final_price'])),
                            $special
                        );
                    }
                }

                $html .= '</td></tr></table>';

                $rssObj->_addEntry(
                    array('title' => $product->getName(), 'link' => $product->getProductUrl(), 'description' => $html)
                );
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     * @return void
     */
    public function addSpecialXmlCallback($args)
    {
        if (!isset(self::$_currentDate)) {
            self::$_currentDate = new \Magento\Framework\Stdlib\DateTime\Date();
        }

        // dispatch event to determine whether the product will eventually get to the result
        $product = new \Magento\Framework\Object(array('allowed_in_rss' => true, 'allowed_price_in_rss' => true));
        $args['product'] = $product;
        $this->_eventManager->dispatch('rss_catalog_special_xml_callback', $args);
        if (!$product->getAllowedInRss()) {
            return;
        }

        // add row to result and determine whether special price is active (less or equal to the final price)
        $row = $args['row'];
        $row['use_special'] = false;
        $row['allowed_price_in_rss'] = $product->getAllowedPriceInRss();
        if (isset(
            $row['special_to_date']
        ) && $row['final_price'] <= $row['special_price'] && $row['allowed_price_in_rss']
        ) {
            $compareDate = self::$_currentDate->compareDate(
                $row['special_to_date'],
                \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT
            );
            if (-1 === $compareDate || 0 === $compareDate) {
                $row['use_special'] = true;
            }
        }

        $args['results'][] = $row;
    }

    /**
     * Function for comparing two items in collection
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    public function sortByStartDate($a, $b)
    {
        return $a['start_date'] > $b['start_date'] ? -1 : ($a['start_date'] < $b['start_date'] ? 1 : 0);
    }
}
