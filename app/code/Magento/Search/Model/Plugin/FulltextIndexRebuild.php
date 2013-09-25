<?php
/**
 * Pluginization of Magento_CatalogSearch_Model_Fulltext model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_Plugin_FulltextIndexRebuild
{
    /**
     * @var Magento_Search_Helper_Data
     */
    protected $_searchHelper;

    /**
     * @var Magento_Search_Model_Catalog_Layer_Filter_Price
     */
    protected $_layerFilterPrice;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Magento_CatalogSearch_Model_Resource_EngineProvider
     */
    protected $_engineProvider = null;

    /**
     * @param Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider
     * @param Magento_Search_Helper_Data $searchHelper
     * @param Magento_Search_Model_Catalog_Layer_Filter_Price $layerFilterPrice
     * @param Magento_Core_Model_CacheInterface $cache
     */
    public function __construct(
        Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider,
        Magento_Search_Helper_Data $searchHelper,
        Magento_Search_Model_Catalog_Layer_Filter_Price $layerFilterPrice,
        Magento_Core_Model_CacheInterface $cache
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_searchHelper = $searchHelper;
        $this->_layerFilterPrice = $layerFilterPrice;
        $this->_cache = $cache;
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param array $arguments
     * @return array
     */
    public function beforeRebuildIndex(array $arguments)
    {
        /* input parameters processing (with default values emulation) */
        if (isset($arguments[1])) {
            list(,$productIds) = $arguments;
        } else {
            $productIds = null;
        }

        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {
            $engine = $this->_engineProvider->get();
            if ($engine->holdCommit() && is_null($productIds)) {
                $engine->setIndexNeedsOptimization();
            }
        }

        return $arguments;
    }

    /**
     * Apply changes in search engine index.
     * Make index optimization if documents were added to index.
     * Allow commit if it was held.
     *
     * @param Magento_CatalogSearch_Model_Fulltext $result
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function afterRebuildIndex(Magento_CatalogSearch_Model_Fulltext $result)
    {
        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {

            $engine = $this->_engineProvider->get();
            if ($engine->allowCommit()) {

                if ($engine->getIndexNeedsOptimization()) {
                    $engine->optimizeIndex();
                } else {
                    $engine->commitChanges();
                }

                /**
                 * Cleaning MAXPRICE cache
                 */
                $cacheTag = $this->_layerFilterPrice->getCacheTag();
                $this->_cache->clean(array($cacheTag));
            }
        }

        return $result;
    }
}