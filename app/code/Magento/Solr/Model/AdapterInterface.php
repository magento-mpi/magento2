<?php
/**
 * Adapter Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model;

interface AdapterInterface
{
    /**
     * Retrieve server status
     *
     * @return  float|bool Actual time taken to ping the server, FALSE if timeout or HTTP error status occurs
     */
    public function ping();

    /**
     * Set advanced index fields prefix
     *
     * @param string $prefix
     * @return void
     */
    public function setAdvancedIndexFieldPrefix($prefix);

    /**
     * Retrieve found document ids from index sorted by relevance
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getIdsByQuery($query, $params);

    /**
     * Search documents in index sorted by relevance
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function search($query, $params);

    /**
     * Collect statistics about specified fields
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getStats($query, $params);

    /**
     * Create Solr Input Documents by specified data
     *
     * @param   array $docData
     * @param   int $storeId
     *
     * @return  array
     */
    public function prepareDocsPerStore($docData, $storeId);

    /**
     * Add prepared Solr Input documents to Solr index
     *
     * @param array $docs
     * @return \Magento\Solr\Model\Adapter\Solr\AbstractSolr
     */
    public function addDocs($docs);

    /**
     * Getter for field to use to determine and enforce document uniqueness
     *
     * @return string
     */
    public function getUniqueKey();

    /**
     * Remove documents from  index
     *
     * @param  int|string|array $docIDs
     * @param  string|array|null $queries if "all" specified and $docIDs are empty, then all documents will be removed
     * @return \Magento\Solr\Model\AdapterInterface
     */
    public function deleteDocs($docIDs = array(), $queries = null);

    /**
     * Retrieve last query number of found results
     *
     * @return int
     */
    public function getLastNumFound();

    /**
     * Perform optimize operation
     * Same as commit operation, but also defragment the index for faster search performance
     *
     * @return object|bool
     */
    public function optimize();

    /**
     * Finalizes all add/deletes made to the index
     *
     * @return object|bool
     */
    public function commit();

    /**
     * Hold commit of changes for adapter
     *
     * @return \Magento\Solr\Model\AdapterInterface
     */
    public function holdCommit();

    /**
     * Allow changes commit for adapter
     *
     * @return \Magento\Solr\Model\AdapterInterface
     */
    public function allowCommit();

    /**
     * Define if third party search engine index needs optimization
     *
     * @param  bool $state
     * @return \Magento\Solr\Model\AdapterInterface
     */
    public function setIndexNeedsOptimization($state);

    /**
     * Check if third party search engine index needs optimization
     *
     * @return bool
     */
    public function getIndexNeedsOptimization();

    /**
     * Store searchable attributes to prevent additional collection load
     *
     * @param   array $attributes
     * @return  \Magento\Solr\Model\AdapterInterface
     */
    public function storeSearchableAttributes(array $attributes);

    /**
     * Retrieve attribute solr field name
     *
     * @param   \Magento\Catalog\Model\Resource\Eav\Attribute|string $attribute
     * @param   string $target - default|sort|nav
     *
     * @return  string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default');
}
