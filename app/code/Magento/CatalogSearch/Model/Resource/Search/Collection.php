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
 * Search collection
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Resource_Search_Collection extends Magento_Catalog_Model_Resource_Product_Collection
{
    /**
     * Attribute collection
     *
     * @var array
     */
    protected $_attributesCollection;

    /**
     * Search query
     *
     * @var string
     */
    protected $_searchQuery;

    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_resource;

    /**
     * Attribute collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory
     * @param Magento_Core_Model_Resource $resource
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
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_resource = $resource;
        parent::__construct(
            $eventManager,
            $logger,
            $fetchStrategy,
            $entityFactory,
            $eavConfig,
            $coreResource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $catalogData,
            $catalogProductFlat,
            $coreStoreConfig
        );
    }

    /**
     * Add search query filter
     *
     * @param string $query
     * @return Magento_CatalogSearch_Model_Resource_Search_Collection
     */
    public function addSearchFilter($query)
    {
        $this->_searchQuery = $query;
        $this->addFieldToFilter('entity_id', array('in'=>new Zend_Db_Expr($this->_getSearchEntityIdsSql($query))));
        return $this;
    }

    /**
     * Retrieve collection of all attributes
     *
     * @return Magento_Data_Collection_Db
     */
    protected function _getAttributesCollection()
    {
        if (!$this->_attributesCollection) {
            $this->_attributesCollection = $this->_attributeCollectionFactory->create()->load();

            foreach ($this->_attributesCollection as $attribute) {
                $attribute->setEntity($this->getEntity());
            }
        }
        return $this->_attributesCollection;
    }

    /**
     * Check attribute is Text and is Searchable
     *
     * @param Magento_Catalog_Model_Entity_Attribute $attribute
     * @return boolean
     */
    protected function _isAttributeTextAndSearchable($attribute)
    {
        if (($attribute->getIsSearchable()
            && !in_array($attribute->getFrontendInput(), array('select', 'multiselect')))
            && (in_array($attribute->getBackendType(), array('varchar', 'text'))
                || $attribute->getBackendType() == 'static')) {
            return true;
        }
        return false;
    }

    /**
     * Check attributes has options and searchable
     *
     * @param Magento_Catalog_Model_Entity_Attribute $attribute
     * @return boolean
     */
    protected function _hasAttributeOptionsAndSearchable($attribute)
    {
        if ($attribute->getIsSearchable()
            && in_array($attribute->getFrontendInput(), array('select', 'multiselect'))) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve SQL for search entities
     *
     * @param unknown_type $query
     * @return string
     */
    protected function _getSearchEntityIdsSql($query)
    {
        $tables = array();
        $selects = array();

        $likeOptions = array('position' => 'any');

        /**
         * Collect tables and attribute ids of attributes with string values
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            /** @var Magento_Catalog_Model_Entity_Attribute $attribute */
            $attributeCode = $attribute->getAttributeCode();
            if ($this->_isAttributeTextAndSearchable($attribute)) {
                $table = $attribute->getBackendTable();
                if (!isset($tables[$table]) && $attribute->getBackendType() != 'static') {
                    $tables[$table] = array();
                }

                if ($attribute->getBackendType() == 'static') {
                    $selects[] = $this->getConnection()->select()
                        ->from($table, 'entity_id')
                        ->where($this->_resourceHelper->getCILike($attributeCode, $this->_searchQuery, $likeOptions));
                } else {
                    $tables[$table][] = $attribute->getId();
                }
            }
        }

        $ifValueId = $this->getConnection()->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
        foreach ($tables as $table => $attributeIds) {
            $selects[] = $this->getConnection()->select()
                ->from(array('t1' => $table), 'entity_id')
                ->joinLeft(
                    array('t2' => $table),
                    $this->getConnection()->quoteInto(
                        't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = ?',
                        $this->getStoreId()),
                    array()
                )
                ->where('t1.attribute_id IN (?)', $attributeIds)
                ->where('t1.store_id = ?', 0)
                ->where($this->_resourceHelper->getCILike($ifValueId, $this->_searchQuery, $likeOptions));
        }

        $sql = $this->_getSearchInOptionSql($query);
        if ($sql) {
            $selects[] = "SELECT * FROM ({$sql}) AS inoptionsql"; // inheritant unions may be inside
        }

        $sql = $this->getConnection()->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
        return $sql;
    }

    /**
     * Retrieve SQL for search entities by option
     *
     * @param unknown_type $query
     * @return string
     */
    protected function _getSearchInOptionSql($query)
    {
        $attributeIds    = array();
        $attributeTables = array();
        $storeId = (int)$this->getStoreId();

        /**
         * Collect attributes with options
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            if ($this->_hasAttributeOptionsAndSearchable($attribute)) {
                $attributeTables[$attribute->getFrontendInput()] = $attribute->getBackend()->getTable();
                $attributeIds[] = $attribute->getId();
            }
        }
        if (empty($attributeIds)) {
            return false;
        }

        $optionTable      = $this->_resource->getTableName('eav_attribute_option');
        $optionValueTable = $this->_resource->getTableName('eav_attribute_option_value');
        $attributesTable  = $this->_resource->getTableName('eav_attribute');

        /**
         * Select option Ids
         */
        $ifStoreId = $this->getConnection()->getIfNullSql('s.store_id', 'd.store_id');
        $ifValue   = $this->getConnection()->getCheckSql('s.value_id > 0', 's.value', 'd.value');
        $select = $this->getConnection()->select()
            ->from(array('d'=>$optionValueTable),
                   array('option_id',
                         'o.attribute_id',
                         'store_id' => $ifStoreId,
                         'a.frontend_input'))
            ->joinLeft(array('s'=>$optionValueTable),
                $this->getConnection()->quoteInto('s.option_id = d.option_id AND s.store_id=?', $storeId),
                array())
            ->join(array('o'=>$optionTable),
                'o.option_id=d.option_id',
                array())
            ->join(array('a' => $attributesTable), 'o.attribute_id=a.attribute_id', array())
            ->where('d.store_id=0')
            ->where('o.attribute_id IN (?)', $attributeIds)
            ->where($this->_resourceHelper->getCILike($ifValue, $this->_searchQuery, array('position' => 'any')));

        $options = $this->getConnection()->fetchAll($select);
        if (empty($options)) {
            return false;
        }

        // build selects of entity ids for specified options ids by frontend input
        $selects = array();
        foreach (array(
            'select'      => 'eq',
            'multiselect' => 'finset')
            as $frontendInput => $condition) {
            if (isset($attributeTables[$frontendInput])) {
                $where = array();
                foreach ($options as $option) {
                    if ($frontendInput === $option['frontend_input']) {
                        $findSet = $this->getConnection()
                            ->prepareSqlCondition('value', array($condition => $option['option_id']));
                        $whereCond = "(attribute_id=%d AND store_id=%d AND {$findSet})";
                        $where[] = sprintf($whereCond, $option['attribute_id'], $option['store_id']);
                    }
                }
                if ($where) {
                    $selects[$frontendInput] = (string)$this->getConnection()->select()
                        ->from($attributeTables[$frontendInput], 'entity_id')
                        ->where(implode(' OR ', $where));
                }
            }
        }

        $sql = $this->getConnection()->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
        return $sql;
    }
}
