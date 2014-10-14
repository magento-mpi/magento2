<?php
/**
 * Pluginization of \Magento\CatalogSearch\Model\Indexer\Fulltext
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Plugin;

class FulltextIndexRebuild
{
    /**
     * Search helper
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $_searchHelper;

    /**
     * Layer filter price
     *
     * @var \Magento\Solr\Model\Layer\Category\Filter\Price
     */
    protected $_layerFilterPrice;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * Engine provider
     *
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Solr\Helper\Data $searchHelper
     * @param \Magento\Solr\Model\Layer\Category\Filter\Price $layerFilterPrice
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Solr\Helper\Data $searchHelper,
        \Magento\Solr\Model\Layer\Category\Filter\Price $layerFilterPrice,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_searchHelper = $searchHelper;
        $this->_layerFilterPrice = $layerFilterPrice;
        $this->_cache = $cache;
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param \Magento\CatalogSearch\Model\Indexer\Fulltext $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteFull(\Magento\CatalogSearch\Model\Indexer\Fulltext $subject)
    {
        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {
            $engine = $this->_engineProvider->get();
            if ($engine->holdCommit()) {
                $engine->setIndexNeedsOptimization();
            }
        }
    }

    /**
     * Apply changes in search engine index.
     * Make index optimization if documents were added to index.
     * Allow commit if it was held.
     *
     * @param \Magento\CatalogSearch\Model\Indexer\Fulltext $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecuteFull(\Magento\CatalogSearch\Model\Indexer\Fulltext $subject)
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
    }
}
