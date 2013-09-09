<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog advanced search model
 *
 * @method Magento_CatalogSearch_Model_Resource_Fulltext _getResource()
 * @method Magento_CatalogSearch_Model_Resource_Fulltext getResource()
 * @method int getProductId()
 * @method Magento_CatalogSearch_Model_Fulltext setProductId(int $value)
 * @method int getStoreId()
 * @method Magento_CatalogSearch_Model_Fulltext setStoreId(int $value)
 * @method string getDataIndex()
 * @method Magento_CatalogSearch_Model_Fulltext setDataIndex(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Fulltext extends Magento_Core_Model_Abstract
{
    const SEARCH_TYPE_LIKE              = 1;
    const SEARCH_TYPE_FULLTEXT          = 2;
    const SEARCH_TYPE_COMBINE           = 3;
    const XML_PATH_CATALOG_SEARCH_TYPE  = 'catalog/search/search_type';

    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_CatalogSearch_Model_Resource_Fulltext');
    }

    /**
     * Regenerate all Stores index
     *
     * Examples:
     * (null, null) => Regenerate index for all stores
     * (1, null)    => Regenerate index for store Id=1
     * (1, 2)       => Regenerate index for product Id=2 and its store view Id=1
     * (null, 2)    => Regenerate index for all store views of product Id=2
     *
     * @param int|null $storeId Store View Id
     * @param int|array|null $productIds Product Entity Id
     *
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        $this->_eventManager->dispatch('catalogsearch_index_process_start', array(
            'store_id'      => $storeId,
            'product_ids'   => $productIds
        ));

        $this->getResource()->rebuildIndex($storeId, $productIds);

        $this->_eventManager->dispatch('catalogsearch_index_process_complete', array());

        return $this;
    }

    /**
     * Delete index data
     *
     * Examples:
     * (null, null) => Clean index of all stores
     * (1, null)    => Clean index of store Id=1
     * (1, 2)       => Clean index of product Id=2 and its store view Id=1
     * (null, 2)    => Clean index of all store views of product Id=2
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        $this->getResource()->cleanIndex($storeId, $productId);
        return $this;
    }

    /**
     * Reset search results cache
     *
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function resetSearchResults()
    {
        $this->getResource()->resetSearchResults();
        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Magento_CatalogSearch_Model_Query $query
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function prepareResult($query = null)
    {
        if (!$query instanceof Magento_CatalogSearch_Model_Query) {
            $query = $this->_catalogSearchData->getQuery();
        }
        $queryText = $this->_catalogSearchData->getQueryText();
        if ($query->getSynonymFor()) {
            $queryText = $query->getSynonymFor();
        }
        $this->getResource()->prepareResult($this, $queryText, $query);
        return $this;
    }

    /**
     * Retrieve search type
     *
     * @param int $storeId
     * @return int
     */
    public function getSearchType($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CATALOG_SEARCH_TYPE, $storeId);
    }





    // Deprecated methods

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     *
     * @param bool $value
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Update category products indexes
     *
     * @deprecated after 1.6.2.0
     *
     * @param array $productIds
     * @param array $categoryIds
     *
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        $this->getResource()->updateCategoryIndex($productIds, $categoryIds);
        return $this;
    }
}
