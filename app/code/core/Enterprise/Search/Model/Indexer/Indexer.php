<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise search model indexer
 *
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Indexer_Indexer
{
    /**
     * Indexation mode that provide commit after all documents are added to index.
     * Products are not visible at front before indexation is not completed.
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL   = 0;

    /**
     * Indexation mode that provide commit after defined amount of products.
     * Products become visible after products bunch is indexed.
     * This is not auto commit using search engine feature.
     *
     * @see Mage_CatalogSearch_Model_Resource_Fulltext::_getSearchableProducts() limitation
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL = 1;

    /**
     * Indexation mode when commit is not provided by Magento at all.
     * Changes will be applied after third party search engine autocommit will be called.
     *
     * @see e.g. /lib/Apache/Solr/conf/solrconfig.xml : <luceneAutoCommit/>, <autoCommit/>
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE  = 2;

    /**
     * Xml path for indexation mode configuration
     */
    const SEARCH_ENGINE_INDEXATION_COMMIT_MODE_XML_PATH = 'catalog/search/engine_commit_mode';






    /**
     * Reindex of catalog search fulltext index using search engine
     *
     * @return Enterprise_Search_Model_Indexer_Indexer
     */
    public function reindexAll()
    {
        $helper = Mage::helper('Enterprise_Search_Helper_Data');
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            /* Change index status to running */
            $indexProcess = Mage::getSingleton('Mage_Index_Model_Indexer')->getProcessByCode('catalogsearch_fulltext');
            if ($indexProcess) {
                $indexProcess->reindexAll();
            }
        }

        return $this;
    }
}
