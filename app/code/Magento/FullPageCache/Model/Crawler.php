<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_FullPageCache_Model_Resource_Crawler _getResource()
 * @method Magento_FullPageCache_Model_Resource_Crawler getResource()
 * @method int getStoreId()
 * @method Magento_FullPageCache_Model_Crawler setStoreId(int $value)
 * @method int getCategoryId()
 * @method Magento_FullPageCache_Model_Crawler setCategoryId(int $value)
 * @method int getProductId()
 * @method Magento_FullPageCache_Model_Crawler setProductId(int $value)
 * @method string getIdPath()
 * @method Magento_FullPageCache_Model_Crawler setIdPath(string $value)
 * @method string getRequestPath()
 * @method Magento_FullPageCache_Model_Crawler setRequestPath(string $value)
 * @method string getTargetPath()
 * @method Magento_FullPageCache_Model_Crawler setTargetPath(string $value)
 * @method int getIsSystem()
 * @method Magento_FullPageCache_Model_Crawler setIsSystem(int $value)
 * @method string getOptions()
 * @method Magento_FullPageCache_Model_Crawler setOptions(string $value)
 * @method string getDescription()
 * @method Magento_FullPageCache_Model_Crawler setDescription(string $value)
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_FullPageCache_Model_Crawler extends Magento_Core_Model_Abstract
{
    /**
     * Crawler settings
     */
    const XML_PATH_CRAWLER_ENABLED     = 'system/page_crawl/enable';
    const XML_PATH_CRAWLER_THREADS     = 'system/page_crawl/threads';
    const XML_PATH_CRAWL_MULTICURRENCY = 'system/page_crawl/multicurrency';
    /**
     * Crawler user agent name
     */
    const USER_AGENT = 'MagentoCrawler';

    /**
     * Store visited URLs by crawler
     *
     * @var array
     */
    protected $_visitedUrls = array();

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_cacheState = $cacheState;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    /**
     * Set resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_FullPageCache_Model_Resource_Crawler');
    }

    /**
     * Get configuration for stores base urls.
     *
     * array(
     *  $index => array(
     *      'store_id'  => $storeId,
     *      'base_url'  => $url,
     *      'cookie'    => $cookie
     *  )
     * )
     *
     * @return array
     */
    public function getStoresInfo()
    {
        $baseUrls = array();
        foreach (Mage::app()->getStores() as $store) {
            $website               = Mage::app()->getWebsite($store->getWebsiteId());
            if (Mage::helper('Magento_WebsiteRestriction_Helper_Data')->getIsRestrictionEnabled($store)) {
                continue;
            }
            $baseUrl               = Mage::app()->getStore($store)->getBaseUrl();
            $defaultCurrency       = Mage::app()->getStore($store)->getDefaultCurrencyCode();
            $defaultWebsiteStore   = $website->getDefaultStore();
            $defaultWebsiteBaseUrl = $defaultWebsiteStore->getBaseUrl();

            $cookie = '';
            if (($baseUrl == $defaultWebsiteBaseUrl) && ($defaultWebsiteStore->getId() != $store->getId())) {
                $cookie = 'store=' . $store->getCode() . ';';
            }

            $baseUrls[] = array(
                'store_id' => $store->getId(),
                'base_url' => $baseUrl,
                'cookie'   => $cookie,
            );
            if ($store->getConfig(self::XML_PATH_CRAWL_MULTICURRENCY)
                && $store->getConfig(Magento_FullPageCache_Model_Processor::XML_PATH_CACHE_MULTICURRENCY)) {
                $currencies = $store->getAvailableCurrencyCodes(true);
                foreach ($currencies as $currencyCode) {
                    if ($currencyCode != $defaultCurrency) {
                        $baseUrls[] = array(
                            'store_id' => $store->getId(),
                            'base_url' => $baseUrl,
                            'cookie'   => $cookie . 'currency=' . $currencyCode . ';'
                        );
                    }
                }
            }
        }
        return $baseUrls;
    }

    /**
     * Crawl all system urls
     *
     * @return Magento_FullPageCache_Model_Crawler
     */
    public function crawl()
    {
        if (!$this->_cacheState->isEnabled('full_page')) {
            return $this;
        }
        $storesInfo  = $this->getStoresInfo();
        $adapter     = new Magento_HTTP_Adapter_Curl();

        foreach ($storesInfo as $info) {
            $options = array(CURLOPT_USERAGENT => self::USER_AGENT);
            $storeId = $info['store_id'];
            $this->_visitedUrls = array();

            if (!Mage::app()->getStore($storeId)->getConfig(self::XML_PATH_CRAWLER_ENABLED)) {
                continue;
            }

            $threads = (int)Mage::app()->getStore($storeId)->getConfig(self::XML_PATH_CRAWLER_THREADS);
            if (!$threads) {
                $threads = 1;
            }
            if (!empty($info['cookie'])) {
                $options[CURLOPT_COOKIE] = $info['cookie'];
            }
            $urls       = array();
            $baseUrl    = $info['base_url'];
            $urlsCount  = $totalCount = 0;
            $urlsPaths  = $this->_getResource()->getUrlsPaths($storeId);
            foreach ($urlsPaths as $urlPath) {
                $url = $baseUrl . $urlPath;
                $urlHash = md5($url);
                if (isset($this->_visitedUrls[$urlHash])) {
                    continue;
                }
                $urls[] = $url;
                $this->_visitedUrls[$urlHash] = true;
                $urlsCount++;
                $totalCount++;
                if ($urlsCount == $threads) {
                    $adapter->multiRequest($urls, $options);
                    $urlsCount = 0;
                    $urls = array();
                }
            }
            if (!empty($urls)) {
                $adapter->multiRequest($urls, $options);
            }
        }
        return $this;
    }
}
