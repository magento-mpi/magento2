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
 * Fulltext Collection
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Resource_Fulltext_Collection extends Magento_Catalog_Model_Resource_Product_Collection
{
    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * Catalog search fulltext
     *
     * @var Magento_CatalogSearch_Model_Fulltext
     */
    protected $_catalogSearchFulltext;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Eav_Model_Factory_Helper $helperFactory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_CatalogSearch_Model_Fulltext $catalogSearchFulltext
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $coreResource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Eav_Model_Resource_Helper $resourceHelper,
        Magento_Eav_Model_Factory_Helper $helperFactory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_CatalogSearch_Model_Fulltext $catalogSearchFulltext
    ) {
        $this->_catalogSearchFulltext = $catalogSearchFulltext;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct(
            $eventManager,
            $logger,
            $fetchStrategy,
            $entityFactory,
            $eavConfig,
            $coreResource,
            $eavEntityFactory,
            $resourceHelper,
            $helperFactory,
            $catalogData,
            $catalogProductFlat,
            $coreStoreConfig
        );
    }

    /**
     * Retrieve query model object
     *
     * @return Magento_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        return $this->_catalogSearchData->getQuery();
    }

    /**
     * Add search query filter
     *
     * @param string $query
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function addSearchFilter($query)
    {
        $this->_catalogSearchFulltext->prepareResult();

        $this->getSelect()->joinInner(
            array('search_result' => $this->getTable('catalogsearch_result')),
            $this->getConnection()->quoteInto(
                'search_result.product_id=e.entity_id AND search_result.query_id=?',
                $this->_getQuery()->getId()
            ),
            array('relevance' => 'relevance')
        );

        return $this;
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($attribute == 'relevance') {
            $this->getSelect()->order("relevance {$dir}");
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Stub method for campatibility with other search engines
     *
     * @return Magento_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function setGeneralDefaultQuery()
    {
        return $this;
    }
}
