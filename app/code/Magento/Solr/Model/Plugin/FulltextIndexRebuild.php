<?php
/**
 * Pluginization of \Magento\CatalogSearch\Model\Indexer\Fulltext
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Solr\Helper\Data $searchHelper,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_searchHelper = $searchHelper;
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
                $this->_cache->clean([\Magento\Solr\Model\Layer\Category\Filter\Price::CACHE_TAG]);
            }
        }
    }
}
