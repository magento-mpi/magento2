<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model;

use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\Query;
use Magento\Core\Model\AbstractModel;
use Magento\Model\Context;
use Magento\Registry;
use Magento\Core\Model\Resource\AbstractResource;
use Magento\Store\Model\Config;
use Magento\Data\Collection\Db;

/**
 * Catalog advanced search model
 *
 * @method \Magento\CatalogSearch\Model\Resource\Fulltext _getResource()
 * @method \Magento\CatalogSearch\Model\Resource\Fulltext getResource()
 * @method int getProductId()
 * @method \Magento\CatalogSearch\Model\Fulltext setProductId(int $value)
 * @method int getStoreId()
 * @method \Magento\CatalogSearch\Model\Fulltext setStoreId(int $value)
 * @method string getDataIndex()
 * @method \Magento\CatalogSearch\Model\Fulltext setDataIndex(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model;

use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\Query;
use Magento\Core\Model\AbstractModel;
use Magento\Model\Context;
use Magento\Registry;
use Magento\Core\Model\Resource\AbstractResource;
use Magento\App\Config\ScopeConfigInterface;
use Magento\Data\Collection\Db;

class Fulltext extends AbstractModel
{
    const SEARCH_TYPE_LIKE              = 1;
    const SEARCH_TYPE_FULLTEXT          = 2;
    const SEARCH_TYPE_COMBINE           = 3;
    const XML_PATH_CATALOG_SEARCH_TYPE  = 'catalog/search/search_type';

    /**
     * Catalog search data
     *
     * @var Data
     */
    protected $_catalogSearchData = null;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $catalogSearchData
     * @param ScopeConfigInterface $coreStoreConfig
     * @param AbstractResource $resource
     * @param Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $catalogSearchData,
        ScopeConfigInterface $coreStoreConfig,
        AbstractResource $resource = null,
        Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogSearchData = $catalogSearchData;
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogSearch\Model\Resource\Fulltext');
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
     * @return $this
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        $this->getResource()->rebuildIndex($storeId, $productIds);
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
     * @return $this
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        $this->getResource()->cleanIndex($storeId, $productId);
        return $this;
    }

    /**
     * Reset search results cache
     *
     * @return $this
     */
    public function resetSearchResults()
    {
        $this->getResource()->resetSearchResults();
        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Query $query
     * @return $this
     */
    public function prepareResult($query = null)
    {
        if (!$query instanceof Query) {
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
        return $this->_storeConfig->getValue(self::XML_PATH_CATALOG_SEARCH_TYPE, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $storeId);
    }

    // Deprecated methods

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     *
     * @param bool $value
     * @return $this
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
     * @return $this
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        $this->getResource()->updateCategoryIndex($productIds, $categoryIds);
        return $this;
    }
}
