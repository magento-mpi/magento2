<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Indexer;

/**
 * Enterprise search model indexer
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Indexer
{
    /**
     * Indexation mode that provide commit after all documents are added to index.
     * Products are not visible at front before indexation is not completed.
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL = 0;

    /**
     * Indexation mode that provide commit after defined amount of products.
     * Products become visible after products bunch is indexed.
     * This is not auto commit using search engine feature.
     *
     * @see \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\Full::getSearchableProducts() limitation
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL = 1;

    /**
     * Indexation mode when commit is not provided by Magento at all.
     * Changes will be applied after third party search engine autocommit will be called.
     *
     * @see e.g. /lib/internal/Apache/Solr/conf/solrconfig.xml : <luceneAutoCommit/>, <autoCommit/>
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE = 2;

    /**
     * Xml path for indexation mode configuration
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH = 'catalog/search/engine_commit_mode';

    /**
     * Search data
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $_searchData = null;

    /** @var \Magento\Indexer\Model\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param \Magento\Solr\Helper\Data $searchData
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        \Magento\Solr\Helper\Data $searchData
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->_searchData = $searchData;
    }

    /**
     * Reindex of catalog search fulltext index using search engine
     *
     * @return $this
     */
    public function reindexAll()
    {
        if ($this->_searchData->isThirdPartyEngineAvailable()) {
            $this->indexerRegistry->get(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)->invalidate();
        }
        return $this;
    }
}
