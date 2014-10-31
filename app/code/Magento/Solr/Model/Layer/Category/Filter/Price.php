<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Category\Filter;

/**
 * Layer price filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Price extends \Magento\Catalog\Model\Layer\Filter\Price
{
    const CACHE_TAG = 'MAXPRICE';

    /**
     * Whether current price interval is divisible
     *
     * @var bool
     */
    protected $_divisible = true;

    /**
     * Ranges faceted data
     *
     * @var array
     */
    protected $_facets = array();

    /**
     * Resource engine
     *
     * @var \Magento\Solr\Model\Resource\Solr\Engine
     */
    protected $_resourceEngine;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Solr\Model\Layer\Category\CacheStateTags
     */
    protected $cacheStateTags;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer\Category $layer
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Solr\Model\Resource\Solr\Engine $resourceEngine
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Solr\Model\Layer\Category\CacheStateTags $cacheStateTags
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Category $layer,
        \Magento\Catalog\Model\Resource\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Solr\Model\Resource\Solr\Engine $resourceEngine,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Solr\Model\Layer\Category\CacheStateTags $cacheStateTags,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        array $data = array()
    ) {
        $this->_resourceEngine = $resourceEngine;
        $this->_cache = $cache;
        $this->cacheStateTags = $cacheStateTags;
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $coreRegistry,
            $scopeConfig,
            $priceCurrency,
            $algorithmFactory,
            $data
        );
    }

    /**
     * Return cache tag for layered price filter
     *
     * @return string
     */
    public function getCacheTag()
    {
        return self::CACHE_TAG;
    }

    /**
     * Get facet field name based on current website and customer group
     *
     * @return string
     */
    protected function _getFilterField()
    {
        $priceField = $this->_resourceEngine->getSearchEngineFieldName('price');

        return $priceField;
    }

    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (!$this->_divisible) {
            return array();
        }

        $isAuto = $this->_scopeConfig->getValue(
            self::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == self::RANGE_CALCULATION_IMPROVED;
        if (!$isAuto && $this->getInterval()) {
            return array();
        }

        $facets = $this->getLayer()->getProductCollection()->getFacetedData($this->_getFilterField());
        $data = array();
        if (!empty($facets)) {
            foreach ($facets as $k => $count) {
                if ($count <= 0) {
                    unset($facets[$k]);
                }
            }

            if (!$isAuto && !empty($facets)) {
                $range = $this->getPriceRange();
            }

            $i = 0;
            $maxIntervalsNumber = $this->getMaxIntervalsNumber();
            $lastSeparator = null;
            foreach ($facets as $key => $count) {
                if (!preg_match('/\[([\d\.\*]+) TO ([\d\.\*]+)\]$/', $key, $separator)) {
                    continue;
                }

                ++$i;
                $label = null;
                $value = null;
                if (isset($this->_facets[$separator[1] . '_' . $separator[2]])) {
                    $separatorLabelValues = $this->_facets[$separator[1] . '_' . $separator[2]];
                    if ($i <= max(1, $maxIntervalsNumber)) {
                        $lastSeparator = $separatorLabelValues[0];
                    }
                    $label = $this->_renderRangeLabel($separatorLabelValues[0], $separatorLabelValues[1]);
                    $value = (empty($separatorLabelValues[0]) ? '' : $separatorLabelValues[0]) .
                        '-' .
                        $separatorLabelValues[1];
                }

                if ($isAuto) {
                    if ($separator[1] == '*') {
                        $separator[1] = '';
                    }
                    if ($separator[2] == '*') {
                        $separator[2] = '';
                    }
                } else {
                    $rangeKey = $separator[2] / $range;

                    $rangeKey = round($rangeKey, 2);
                    $separator[1] = $rangeKey == 1 ? '' : ($rangeKey - 1) * $range;
                    $separator[2] = $key == null ? '' : $rangeKey * $range;
                    // checking max number of intervals
                    if ($i > 1 && $i > $maxIntervalsNumber) {
                        --$i;
                        $count += $data[$i - 1]['count'];
                        $separator[1] = $data[$i - 1]['from'];
                        $label = $value = null;
                    } elseif (!empty($separator[2]) && $separator[2] > $this->getMaxPriceInt()) {
                        $label = $value = null;
                        $separator[2] = '';
                    }
                }

                $data[$i - 1] = array(
                    'label' => is_null(
                        $label
                    ) ? $this->_renderRangeLabel(
                        empty($separator[1]) ? 0 : $separator[1] * $this->getCurrencyRate(),
                        empty($separator[2]) ? $separator[2] : $separator[2] * $this->getCurrencyRate()
                    ) : $label,
                    'value' => (is_null(
                        $value
                    ) ? $separator[1] . '-' . $separator[2] : $value) . $this->_getAdditionalRequestData(),
                    'count' => $count,
                    'from' => $separator[1],
                    'to' => $separator[2]
                );
            }

            if (isset($data[$i - 1]) && $data[$i - 1]['from'] != $data[$i - 1]['to']) {
                $upperIntervalLimit = '';
                $appliedInterval = $this->getInterval();
                if ($appliedInterval) {
                    $upperIntervalLimit = $appliedInterval[1];
                }
                if (is_null($value)) {
                    $data[$i -
                        1]['value'] = $lastSeparator .
                        '-' .
                        $upperIntervalLimit .
                        $this->_getAdditionalRequestData();
                }
                if (is_null($label)) {
                    $data[$i - 1]['label'] = $this->_renderRangeLabel(
                        empty($lastSeparator) ? 0 : $lastSeparator,
                        $upperIntervalLimit
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Get maximum price from layer products set using cache
     *
     * @return float
     */
    public function getMaxPriceInt()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $uniquePart = strtoupper(md5(serialize($searchParams) . '_' . $this->getCurrencyRate()));
        $cacheKey = 'MAXPRICE_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = $this->_cache->load($cacheKey);
        if (!$cachedData) {
            $stats = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());

            $max = $stats[$this->_getFilterField()]['max'];
            if (!is_numeric($max)) {
                $max = \Magento\Catalog\Model\Layer\Filter\Price::getMaxPriceInt();
            } else {
                $max = floor($max * $this->getCurrencyRate());
            }

            $cachedData = $max;
            $tags = $this->cacheStateTags->getList($this->getLayer()->getCurrentCategory());
            $tags[] = $this->getCacheTag();
            $this->_cache->save($cachedData, $cacheKey, $tags);
        }

        return $cachedData;
    }

    /**
     * Get data with price separators
     *
     * @return array
     */
    protected function _getSeparators()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $intervalParams = $this->getInterval();
        $intervalParams = $intervalParams ? $intervalParams[0] . '-' . $intervalParams[1] : '';
        $uniquePart = strtoupper(
            md5(serialize($searchParams) . '_' . $this->getCurrencyRate() . '_' . $intervalParams)
        );
        $cacheKey = 'PRICE_SEPARATORS_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = $this->_cache->load($cacheKey);
        if (!$cachedData) {
            $statistics = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());
            $statistics = $statistics[$this->_getFilterField()];

            $appliedInterval = $this->getInterval();
            if ($appliedInterval && ($statistics['count'] <= $this->getIntervalDivisionLimit() ||
                $appliedInterval[0] == $appliedInterval[1] ||
                $appliedInterval[1] === '0')
            ) {
                $this->_priceAlgorithm->setStatistics(0, 0, 0, 0);
                $this->_divisible = false;
            } else {
                if ($appliedInterval) {
                    $this->_priceAlgorithm->setLimits($appliedInterval[0], $appliedInterval[1]);
                }
                $this->setStatistics(
                    round($statistics['min'] * $this->getCurrencyRate(), 2),
                    round($statistics['max'] * $this->getCurrencyRate(), 2),
                    $statistics['stddev'] * $this->getCurrencyRate(),
                    $statistics['count']
                );
            }

            $cachedData = array();
            foreach ($this->_priceAlgorithm->calculateSeparators() as $separator) {
                $cachedData[] = $separator['from'] . '-' . $separator['to'];
            }
            $cachedData = implode(',', $cachedData);

            $tags = $this->cacheStateTags->getList($this->getLayer()->getCurrentCategory());
            $tags[] = $this->getCacheTag();
            $this->_cache->save($cachedData, $cacheKey, $tags);
        }

        if (!$cachedData) {
            return array();
        }

        $cachedData = explode(',', $cachedData);
        foreach ($cachedData as $k => $v) {
            $cachedData[$k] = explode('-', $v);
        }

        return $cachedData;
    }

    /**
     * Prepare faceted value
     *
     * @param float $value
     * @param bool $decrease
     * @return float
     */
    protected function _prepareFacetedValue($value, $decrease = true)
    {
        // rounding issue
        if ($this->getCurrencyRate() > 1) {
            if ($decrease) {
                $value -= \Magento\Catalog\Model\Resource\Layer\Filter\Price::MIN_POSSIBLE_PRICE / 10;
            } else {
                $value += \Magento\Catalog\Model\Resource\Layer\Filter\Price::MIN_POSSIBLE_PRICE / 10;
            }
            $value /= $this->getCurrencyRate();
        } else {
            $value /= $this->getCurrencyRate();
            if ($decrease) {
                $value -= \Magento\Catalog\Model\Resource\Layer\Filter\Price::MIN_POSSIBLE_PRICE / 10;
            } else {
                $value += \Magento\Catalog\Model\Resource\Layer\Filter\Price::MIN_POSSIBLE_PRICE / 10;
            }
        }
        return round($value, 3);
    }

    /**
     * Prepare price range to be added to facet conditions
     *
     * @param string|float $from
     * @param string|float $to
     * @return array
     */
    protected function _prepareFacetRange($from, $to)
    {
        if (empty($from)) {
            $from = '*';
        }

        if ($to === '') {
            $to = '*';
        } else {
            if ($to == $from || $to == 0 && $from == '*') {
                $to = $this->_prepareFacetedValue($to, false);
            } else {
                $to = $this->_prepareFacetedValue($to);
            }
        }

        if ($from != '*') {
            $from = $this->_prepareFacetedValue($from);
        }
        return array('from' => $from, 'to' => $to);
    }

    /**
     * Add params to faceted search generated by algorithm
     *
     * @return void
     */
    protected function _addCalculatedFacetCondition()
    {
        $priceFacets = array();
        $this->_facets = array();
        foreach ($this->_getSeparators() as $separator) {
            $facetedRange = $this->_prepareFacetRange($separator[0], $separator[1]);
            $this->_facets[$facetedRange['from'] . '_' . $facetedRange['to']] = $separator;
            $priceFacets[] = $facetedRange;
        }
        $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Catalog\Model\Layer\Filter\Price
     */
    public function addFacetCondition()
    {
        $range = $this->_scopeConfig->getValue(
            self::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (\Magento\Catalog\Model\Layer\Filter\Price::RANGE_CALCULATION_IMPROVED == $range) {
            return $this->_addCalculatedFacetCondition();
        }

        $this->_facets = array();
        $range = $this->getPriceRange();
        $maxPrice = $this->getMaxPriceInt();
        if ($maxPrice >= 0) {
            $priceFacets = array();
            $facetCount = ceil($maxPrice / $range);

            for ($i = 0; $i < $facetCount + 1; $i++) {
                $separator = array($i * $range, ($i + 1) * $range);
                $facetedRange = $this->_prepareFacetRange($separator[0], $separator[1]);
                $this->_facets[$facetedRange['from'] . '_' . $facetedRange['to']] = $separator;
                $priceFacets[] = $facetedRange;
            }

            $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
        }

        return $this;
    }

    /**
     * Apply price range filter to collection
     *
     * @return \Magento\Catalog\Model\Layer\Filter\Price
     */
    protected function _applyPriceRange()
    {
        list($from, $to) = $this->getInterval();
        $this->getLayer()->getProductCollection()->addFqFilter(
            array($this->_getFilterField() => $this->_prepareFacetRange($from, $to))
        );

        return $this;
    }
}
