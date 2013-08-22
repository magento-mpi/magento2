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
 * Catalog search query model
 *
 * @method Magento_CatalogSearch_Model_Resource_Query _getResource()
 * @method Magento_CatalogSearch_Model_Resource_Query getResource()
 * @method string getQueryText()
 * @method Magento_CatalogSearch_Model_Query setQueryText(string $value)
 * @method int getNumResults()
 * @method Magento_CatalogSearch_Model_Query setNumResults(int $value)
 * @method int getPopularity()
 * @method Magento_CatalogSearch_Model_Query setPopularity(int $value)
 * @method string getRedirect()
 * @method Magento_CatalogSearch_Model_Query setRedirect(string $value)
 * @method string getSynonymFor()
 * @method Magento_CatalogSearch_Model_Query setSynonymFor(string $value)
 * @method int getDisplayInTerms()
 * @method Magento_CatalogSearch_Model_Query setDisplayInTerms(int $value)
 * @method int getIsActive()
 * @method Magento_CatalogSearch_Model_Query setIsActive(int $value)
 * @method int getIsProcessed()
 * @method Magento_CatalogSearch_Model_Query setIsProcessed(int $value)
 * @method string getUpdatedAt()
 * @method Magento_CatalogSearch_Model_Query setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Query extends Magento_Core_Model_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'catalogsearch_query';

    /**
     * Event object key name
     *
     * @var string
     */
    protected $_eventObject = 'catalogsearch_query';

    const CACHE_TAG                     = 'SEARCH_QUERY';
    const XML_PATH_MIN_QUERY_LENGTH     = 'catalog/search/min_query_length';
    const XML_PATH_MAX_QUERY_LENGTH     = 'catalog/search/max_query_length';
    const XML_PATH_MAX_QUERY_WORDS      = 'catalog/search/max_query_words';

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogSearch_Model_Resource_Query');
    }

    /**
     * Retrieve search collection
     *
     * @return Magento_CatalogSearch_Model_Resource_Search_Collection
     */
    public function getSearchCollection()
    {
        return Mage::getResourceModel('Magento_CatalogSearch_Model_Resource_Search_Collection');
    }

    /**
     * Retrieve collection of search results
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getResultCollection()
    {
        $collection = $this->getData('result_collection');
        if (is_null($collection)) {
            $collection = $this->getSearchCollection();

            $text = $this->getSynonymFor();
            if (!$text) {
                $text = $this->getQueryText();
            }

            $collection->addSearchFilter($text)
                ->addStoreFilter()
                ->addMinimalPrice()
                ->addTaxPercents();
            $this->setData('result_collection', $collection);
        }
        return $collection;
    }

    /**
     * Retrieve collection of suggest queries
     *
     * @return Magento_CatalogSearch_Model_Resource_Query_Collection
     */
    public function getSuggestCollection()
    {
        $collection = $this->getData('suggest_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('Magento_CatalogSearch_Model_Resource_Query_Collection')
                ->setStoreId($this->getStoreId())
                ->setQueryFilter($this->getQueryText());
            $this->setData('suggest_collection', $collection);
        }
        return $collection;
    }

    /**
     * Load Query object by query string
     *
     * @param string $text
     * @return Magento_CatalogSearch_Model_Query
     */
    public function loadByQuery($text)
    {
        $this->_getResource()->loadByQuery($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Load Query object only by query text (skip 'synonym For')
     *
     * @param string $text
     * @return Magento_CatalogSearch_Model_Query
     */
    public function loadByQueryText($text)
    {
        $this->_getResource()->loadByQueryText($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return Magento_CatalogSearch_Model_Query
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$storeId = $this->getData('store_id')) {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Prepare save query for result
     *
     * @return Magento_CatalogSearch_Model_Query
     */
    public function prepare()
    {
        if (!$this->getId()) {
            $this->setIsActive(0);
            $this->setIsProcessed(0);
            $this->save();
            $this->setIsActive(1);
        }

        return $this;
    }

    /**
     * Retrieve minimum query length
     *
     * @return int
     */
    public function getMinQueryLength()
    {
        return Mage::getStoreConfig(self::XML_PATH_MIN_QUERY_LENGTH, $this->getStoreId());
    }

    /**
     * Retrieve maximum query length
     *
     * @return int
     */
    public function getMaxQueryLength()
    {
        return Mage::getStoreConfig(self::XML_PATH_MAX_QUERY_LENGTH, $this->getStoreId());
    }

    /**
     * Retrieve maximum query words for like search
     *
     * @return int
     */
    public function getMaxQueryWords()
    {
        return Mage::getStoreConfig(self::XML_PATH_MAX_QUERY_WORDS, $this->getStoreId());
    }
}
