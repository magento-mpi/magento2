<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Indexer;

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
     * @see \Magento\CatalogSearch\Model\Resource\Fulltext::_getSearchableProducts() limitation
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL = 1;

    /**
     * Indexation mode when commit is not provided by Magento at all.
     * Changes will be applied after third party search engine autocommit will be called.
     *
     * @see e.g. /lib/Apache/Solr/conf/solrconfig.xml : <luceneAutoCommit/>, <autoCommit/>
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE = 2;

    /**
     * Xml path for indexation mode configuration
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH = 'catalog/search/engine_commit_mode';

    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\Search\Helper\Data $searchData
     */
    public function __construct(\Magento\Index\Model\Indexer $indexer, \Magento\Search\Helper\Data $searchData)
    {
        $this->_indexer = $indexer;
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
            /* Change index status to running */
            $indexProcess = $this->_indexer->getProcessByCode('catalogsearch_fulltext');
            if ($indexProcess) {
                $indexProcess->reindexAll();
            }
        }

        return $this;
    }
}
