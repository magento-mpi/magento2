<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Flat Indexer Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Flat_Indexer extends Mage_Core_Model_Resource_Db_Abstract
{
    const XML_NODE_MAX_INDEX_COUNT  = 'global/catalog/product/flat/max_index_count';
    const XML_NODE_ATTRIBUTE_NODES  = 'global/catalog/product/flat/attribute_nodes';

    /**
     * Attribute codes for flat
     *
     * @var array
     */
    protected $_attributeCodes;

    /**
     * Attribute objects for flat cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Required system attributes for preload
     *
     * @var array
     */
    protected $_systemAttributes     = array('status', 'required_options', 'tax_class_id', 'weight');

    /**
     * Eav Catalog_Product Entity Type Id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Flat table columns cache
     *
     * @var array
     */
    protected $_columns;

    /**
     * Flat table indexes cache
     *
     * @var array
     */
    protected $_indexes;

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes;

    /**
     * Exists flat tables cache
     *
     * @var array
     */
    protected $_existsFlatTables     = array();

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Rebuild Catalog Product Flat Data
     *
     * @param Mage_Core_Model_Store|int $store
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function rebuild($store = null)
    {
        if ($store === null) {
            foreach (Mage::app()->getStores() as $store) {
                $this->rebuild($store->getId());
            }
            $flag = $this->getFlatHelper()->getFlag();
            $flag->setIsBuild(true)->save();

            return $this;
        }

        $storeId = (int)Mage::app()->getStore($store)->getId();

        $this->prepareFlatTable($storeId);
        $this->cleanNonWebsiteProducts($storeId);
        $this->updateStaticAttributes($storeId);
        $this->updateEavAttributes($storeId);
        $this->updateEventAttributes($storeId);
        $this->updateRelationProducts($storeId);
        $this->cleanRelationProducts($storeId);

        return $this;
    }

    /**
     * Retrieve Catalog Product Flat helper
     *
     * @return Mage_Catalog_Helper_Product_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('catalog/product_flat');
    }

    /**
     * Retrieve attribute codes using for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        if ($this->_attributeCodes === null) {
            $adapter               = $this->_getReadAdapter();
            $this->_attributeCodes = array();

            $attributeNodes = Mage::getConfig()
                ->getNode(self::XML_NODE_ATTRIBUTE_NODES)
                ->children();
            foreach ($attributeNodes as $node) {
                $attributes = Mage::getConfig()->getNode((string)$node)->asArray();
                $attributes = array_keys($attributes);
                $this->_systemAttributes = array_unique(array_merge($attributes, $this->_systemAttributes));
            }

            $bind = array(
                'backend_type'      => 'static',
                'entity_type_id'    => $this->getEntityTypeId()
            );

            $select = $adapter->select()
                ->from(array('main_table' => $this->getTable('eav/attribute')))
                ->join(
                    array('additional_table' => $this->getTable('catalog/eav_attribute')),
                    'additional_table.attribute_id = main_table.attribute_id'
                )
                ->where('main_table.entity_type_id = :entity_type_id')
                ->where('main_table.backend_type = :backend_type')
                ->where('main_table.attribute_code IN(?)', $this->_systemAttributes)
                ->where('additional_table.used_in_product_listing = ?', 1)
                ->where('additional_table.used_for_sort_by = ?', 1);

            if ($this->getFlatHelper()->isAddFilterableAttributes()) {
                $select->where('additional_table.is_filterable > ?', 0);
            }

            $attributesData = $adapter->fetchAll($select, $bind);
            Mage::getSingleton('eav/config')
                ->importAttributesData($this->getEntityType(), $attributesData);

            foreach ($attributesData as $data) {
                $this->_attributeCodes[$data['attribute_id']] = $data['attribute_code'];
            }
            unset($attributesData);
        }

        return $this->_attributeCodes;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'catalog_product';
    }

    /**
     * Retrieve Catalog Entity Type Id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = Mage::getResourceModel('catalog/config')
                ->getEntityTypeId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve attribute objects for flat
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = array();
            $attributeCodes    = $this->getAttributeCodes(false);
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();

            foreach ($attributeCodes as $attributeCode) {
                $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute($this->getEntityType(), $attributeCode)
                    ->setEntity($entity);
                $this->_attributes[$attributeCode] = $attribute;
            }
        }
        return $this->_attributes;
    }

    /**
     * Retrieve loaded attribute by code
     *
     * @param string $attributeCode
     * @throws Mage_Core_Exception
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (!isset($attributes[$attributeCode])) {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode($this->getEntityTypeId(), $attributeCode);
            if (!$attribute->getId()) {
                Mage::throwException(Mage::helper('catalog')->__('Invalid attribute %s', $attributeCode));
            }
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();
            $attribute->setEntity($entity);

            return $attribute;
        }

        return $attributes[$attributeCode];
    }

    /**
     * Retrieve Catalog Product Flat Table name
     *
     * @param int $storeId
     * @return string
     */
    public function getFlatTableName($storeId)
    {
        return sprintf('%s_%s', $this->getTable('catalog/product_flat'), $storeId);
    }

    /**
     * Retrieve catalog product flat table columns array
     *
     * @return array
     */
    public function getFlatColumns()
    {
        if ($this->_columns === null) {
            $this->_columns = array();
            $this->_columns['entity_id'] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'length'    => null,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            );
            if ($this->getFlatHelper()->isAddChildData()) {
                $this->_columns['child_id'] = array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'length'    => null,
                    'unsigned'  => true,
                    'nullable'   => true,
                    'default'   => null,
                    'primary'   => true,
                    'comment'   => 'Child Id'
                );
                $this->_columns['is_child'] = array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                    'length'    => 1,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => 0,
                    'comment'   => 'Checks If Entity Is Child'
                );
            }
            $this->_columns['attribute_set_id'] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'length'    => 5,
                'unsigned'  => true,
                'nullable'   => false,
                'default'   => 0,
                'comment'   => 'Attribute Set Id'
            );
            $this->_columns['type_id'] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'unsigned'  => false,
                'nullable'  => false,
                'default'   => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'comment'   => 'Type Id'
            );

            foreach ($this->getAttributes() as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                $columns = $attribute
                    ->setFlatAddFilterableAttributes($this->getFlatHelper()->isAddFilterableAttributes())
                    ->setFlatAddChildData($this->getFlatHelper()->isAddChildData())
                    ->getFlatColumns();
                if ($columns !== null) {
                    $this->_columns = array_merge($this->_columns, $columns);
                }
            }

            $columnsObject = new Varien_Object();
            $columnsObject->setColumns($this->_columns);
            Mage::dispatchEvent('catalog_product_flat_prepare_columns', array(
                'columns'   => $columnsObject
            ));
            $this->_columns = $columnsObject->getColumns();
        }

        return $this->_columns;
    }

    /**
     * Retrieve catalog product flat table indexes array
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        if ($this->_indexes === null) {
            $this->_indexes = array();

            if ($this->getFlatHelper()->isAddChildData()) {
                $this->_indexes['PRIMARY'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id', 'child_id')
                );
                $this->_indexes['IDX_CHILD'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                    'fields' => array('child_id')
                );
                $this->_indexes['IDX_IS_CHILD'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                    'fields' => array('entity_id', 'is_child')
                );
            } else {
                $this->_indexes['PRIMARY'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id')
                );
            }
            $this->_indexes['IDX_TYPE_ID'] = array(
                'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                'fields' => array('type_id')
            );
            $this->_indexes['IDX_ATTRIBUTE_SET'] = array(
                'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                'fields' => array('attribute_set_id')
            );

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute */
                $indexes = $attribute
                    ->setFlatAddFilterableAttributes($this->getFlatHelper()->isAddFilterableAttributes())
                    ->setFlatAddChildData($this->getFlatHelper()->isAddChildData())
                    ->getFlatIndexes();
                if ($indexes !== null) {
                    $this->_indexes = array_merge($this->_indexes, $indexes);
                }
            }

            $indexesObject = new Varien_Object();
            $indexesObject->setIndexes($this->_indexes);
            Mage::dispatchEvent('catalog_product_flat_prepare_indexes', array(
                'indexes'   => $indexesObject
            ));
            $this->_indexes = $indexesObject->getIndexes();
        }

        return $this->_indexes;
    }

    /**
     * Compare Flat style with Describe style columns
     * If column a different - return false
     *
     * @param array $column
     * @param array $describe
     * @return bool
     */
    protected function _compareColumnProperties($column, $describe)
    {
        return Mage::getResourceHelper('catalog')->compareIndexColumnProperties($column, $describe);
    }

    /**
     * Retrieve column definition fragment
     * @deprecated since 1.5.0.0
     *
     * Example: `field_name` smallint(5) unsigned NOT NULL default '0'
     *
     * @param string $fieldName
     * @param array $fieldProp
     * @return string
     */
    protected function _sqlColunmDefinition($fieldName, $fieldProp)
    {
        $fieldNameQuote = $this->_getWriteAdapter()->quoteIdentifier($fieldName);

        /**
         * Process the case when 'is_null' prohibits null value, and 'default' proposed to be null
         * It just means that default value not specified
         */
        if ($fieldProp['is_null'] === false && $fieldProp['default'] === null) {
            $defaultValue = '';
        } else {
            $defaultValue = $fieldProp['default'] === null ? ' DEFAULT NULL' : $this->_getReadAdapter()
                ->quoteInto(' DEFAULT ?', $fieldProp['default']);
        }

        return "{$fieldNameQuote} {$fieldProp['type']}"
            . ($fieldProp['unsigned'] ? ' UNSIGNED' : '')
            . ($fieldProp['extra'] ? ' ' . $fieldProp['extra'] : '')
            . ($fieldProp['is_null'] === false ? ' NOT NULL' : '')
            . $defaultValue;
    }

    /**
     * Retrieve index definition fragment
     * @deprecated since 1.5.0.0
     *
     * Example: INDEX `IDX_NAME` (`field_id`)
     *
     * @param string $indexName
     * @param array $indexProp
     * @return string
     */
    protected function _sqlIndexDefinition($indexName, $indexProp)
    {
        $fields = $indexProp['fields'];
        if (is_array($fields)) {
            $fieldSql = array();
            foreach ($fields as $field) {
                $fieldSql[] = $this->_getReadAdapter()->quoteIdentifier($field);
            }
            $fieldSql = join(',', $fieldSql);
        }
        else {
            $fieldSql = $this->_getReadAdapter()->quoteIdentifier($fields);
        }

        $indexNameQuote = $this->_getReadAdapter()->quoteIdentifier($indexName);
        switch (strtolower($indexProp['type'])) {
            case 'primary':
                $condition = 'PRIMARY KEY';
                break;
            case 'unique':
                $condition = 'UNIQUE ' . $indexNameQuote;
                break;
            case 'fulltext':
                $condition = 'FULLTEXT ' . $indexNameQuote;
                break;
            default:
                $condition = 'INDEX ' . $indexNameQuote;
                break;
        }

        return sprintf('%s (%s)', $condition, $fieldSql);
    }

    /**
     * Retrieve UNIQUE HASH for a Table foreign key
     *
     * @param string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return Mage::getSingleton('core/resource')
            ->getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * Prepare flat table for store
     *
     * @param int $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function prepareFlatTable($storeId)
    {
        $columns    = $this->getFlatColumns();
        $indexes    = $this->getFlatIndexes();
        $maxIndex   = Mage::getConfig()->getNode(self::XML_NODE_MAX_INDEX_COUNT);

        if (count($indexes) > $maxIndex) {
            Mage::throwException(Mage::helper('catalog')->__("The Flat Catalog module has a limit of %2\$d filterable and/or sortable attributes. Currently there are %1\$d of them. Please reduce the number of filterable/sortable attributes in order to use this module", count($indexes), $maxIndex));
        }

        $adapter        = $this->_getWriteAdapter();
        $tableName      = $this->getFlatTableName($storeId);
        /*
         * Apply new names For indexes;
         */
        $indexNames = array();
        $indexProps = array_values($indexes);
        foreach ($indexProps as $propId => $indexProp) {
            $indexNames[$propId] = $adapter->getIndexName($tableName, $indexProp['fields'], $indexProp['type']);
        }
        $indexes = array_combine($indexNames, $indexProps);

        $foreignEntityKey = $this->getFkName($tableName, 'entity_id', 'catalog/product', 'entity_id');
        $foreignChildKey  = $this->getFkName($tableName, 'child_id', 'catalog/product', 'entity_id');

        if (!$this->_isFlatTableExists($storeId)) {
            /** @var $table Varien_Db_Ddl_Table */
            $table = $adapter->newTable($tableName);
            foreach ($columns as $fieldName => $fieldProp) {
                $table->addColumn($fieldName, $fieldProp['type'], $fieldProp['length'], array(
                    'nullable' => isset($fieldProp['nullable']) ? (bool)$fieldProp['nullable'] : false,
                    'unsigned' => isset($fieldProp['unsigned']) ? (bool)$fieldProp['unsigned'] : false,
                    'default'  => $fieldProp['default'],
                    'primary'  => false,
                ), $fieldProp['comment']);
            }

            foreach ($indexes as $indexName => $indexProp) {
                if ($indexName == 'PRIMARY') {
                    continue;
                }
                $table->addIndex($indexName, $indexProp['fields'], array('type' => $indexProp['type']));
            }

            $table->addForeignKey($foreignEntityKey,
                'entity_id', $this->getTable('catalog/product'), 'entity_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

            if ($this->getFlatHelper()->isAddChildData()) {
                $table->addForeignKey($foreignChildKey,
                    'child_id', $this->getTable('catalog/product'), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
            }
            $table->setComment("Catalog Product Flat (Store {$storeId})");

            $adapter->createTable($table);

            $this->_existsFlatTables[$storeId] = true;
        } else {
            $adapter->resetDdlCache($tableName);
            $describe   = $adapter->describeTable($tableName);
            $indexList  = $adapter->getIndexList($tableName);
            $addColumns     = array_diff_key($columns, $describe);
            $dropColumns    = array_diff_key($describe, $columns);
            $modifyColumns  = array();

            $addIndexes     = array_diff_key($indexes, $indexList);
            $dropIndexes    = array_diff_key($indexList, $indexes);
            $addConstraints = array();

            $addConstraints[$foreignEntityKey] = array(
                'table_index'   => 'entity_id',
                'ref_table'     => $this->getTable('catalog/product'),
                'ref_index'     => 'entity_id',
                'on_update'     => Varien_Db_Ddl_Table::ACTION_CASCADE,
                'on_delete'     => Varien_Db_Ddl_Table::ACTION_CASCADE
            );

            $isAddChildData = $this->getFlatHelper()->isAddChildData();
            if (!$isAddChildData && isset($describe['is_child'])) {
                $adapter->delete($tableName, array('is_child = ?' => 1));
                $adapter->dropForeignKey($tableName, $foreignChildKey);
            }
            if ($isAddChildData && !isset($describe['is_child'])) {
                $adapter->truncate($tableName);
                $dropIndexes['PRIMARY'] = $indexList['PRIMARY'];
                $addIndexes['PRIMARY']  = $indexes['PRIMARY'];

                $addConstraints[$foreignChildKey] = array(
                    'table_index'   => 'child_id',
                    'ref_table'     => $this->getTable('catalog/product'),
                    'ref_index'     => 'entity_id',
                    'on_update'     => Varien_Db_Ddl_Table::ACTION_CASCADE,
                    'on_delete'     => Varien_Db_Ddl_Table::ACTION_CASCADE
                );
            }

            foreach ($columns as $field => $fieldProp) {
                if (isset($describe[$field])
                    && !$this->_compareColumnProperties($fieldProp, $describe[$field])) {
                    $modifyColumns[$field] = $fieldProp;
                }
            }

            foreach ($indexList as $indexName => $indexProp) {
                if (isset($indexes[$indexName]) && ($indexes[$indexName]['type'] != $indexProp['type'])) {
                    $dropIndexes[$indexName] = $indexProp;
                    $addIndexes[$indexName] = $indexes[$indexName];
                }
            }

            foreach (array_keys($adapter->getForeignKeys($tableName)) as $constraintName) {
                $adapter->dropForeignKey($tableName, $constraintName);
            }
            // drop indexes
            foreach (array_keys($dropIndexes) as $indexName) {
                $adapter->dropIndex($tableName, $indexName);
            }

            // drop columns
            foreach (array_keys($dropColumns) as $columnName) {
                $adapter->dropColumn($tableName, $columnName);
            }

            // modify column
            foreach ($modifyColumns as $columnName => $columnProp) {
                if (!isset($columnProp['comment'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                } else {
                    $columnProp['COMMENT'] = $columnProp['comment'];
                }
                $adapter->changeColumn($tableName, $columnName, $columnName, $columnProp);
            }

            // add columns
            foreach ($addColumns as $columnName => $columnProp) {
                if (!isset($columnProp['comment'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                } else {
                    $columnProp['COMMENT'] = $columnProp['comment'];
                }
                $adapter->addColumn($tableName, $columnName, $columnProp);
            }

            // add indexes
            foreach ($addIndexes as $indexName => $indexProp) {
                $adapter->addIndex($tableName, $indexName, $indexProp['fields'], $indexProp['type']);
            }

            foreach ($addConstraints as $constraintName => $constraintProp) {
                $adapter->addForeignKey($constraintName, $tableName,
                    $constraintProp['table_index'],
                    $constraintProp['ref_table'],
                    $constraintProp['ref_index'],
                    $constraintProp['on_delete'],
                    $constraintProp['on_update']
                );
            }
        }

        return $this;
    }

    /**
     * Add or Update static attributes
     *
     * @param int $storeId
     * @param int|array $productIds update only product(s)
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function updateStaticAttributes($storeId, $productIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter   = $this->_getWriteAdapter();
        $websiteId = (int)Mage::app()->getStore($storeId)->getWebsite()->getId();
        /* @var $status Mage_Eav_Model_Entity_Attribute */
        $status    = $this->getAttribute('status');

        $fieldList  = array('entity_id', 'type_id', 'attribute_set_id');
        $colsList   = array('entity_id', 'type_id', 'attribute_set_id');
        if ($this->getFlatHelper()->isAddChildData()) {
            $fieldList = array_merge($fieldList, array('child_id', 'is_child'));
            $isChild   = new Zend_Db_Expr('0');
            $colsList  = array_merge($colsList, array('entity_id', $isChild));
        }

        $columns    = $this->getFlatColumns();
        $bind       = array(
            'website_id'     => $websiteId,
            'store_id'       => $storeId,
            'entity_type_id' => (int)$status->getEntityTypeId(),
            'attribute_id'   => (int)$status->getId()
        );

        $fieldExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
        $select     = $this->_getWriteAdapter()->select()
            ->from(array('e' => $this->getTable('catalog/product')), $colsList)
            ->join(
                array('wp' => $this->getTable('catalog/product_website')),
                'e.entity_id = wp.product_id AND wp.website_id = :website_id',
                array())
            ->joinLeft(
                array('t1' => $status->getBackend()->getTable()),
                'e.entity_id = t1.entity_id',
                array())
            ->joinLeft(
                array('t2' => $status->getBackend()->getTable()),
                't2.entity_id = t1.entity_id'
                    . ' AND t1.entity_type_id = t2.entity_type_id'
                    . ' AND t1.attribute_id = t2.attribute_id'
                    . ' AND t2.store_id = :store_id',
                array())
            ->where('t1.entity_type_id = :entity_type_id')
            ->where('t1.attribute_id = :attribute_id')
            ->where('t1.store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID)
            ->where("{$fieldExpr} = ?", Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        foreach ($this->getAttributes() as $attributeCode => $attribute) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() == 'static') {
                if (!isset($columns[$attributeCode])) {
                    continue;
                }
                $fieldList[] = $attributeCode;
                $select->columns($attributeCode, 'e');
            }
        }

        if ($productIds !== null) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $sql = $select->insertFromSelect($this->getFlatTableName($storeId), $fieldList);
        $adapter->query($sql, $bind);

        return $this;
    }

    /**
     * Remove non website products
     *
     * @param int $storeId
     * @param int|array $productIds
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function cleanNonWebsiteProducts($storeId, $productIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $websiteId = (int)Mage::app()->getStore($storeId)->getWebsite()->getId();
        $adapter   = $this->_getWriteAdapter();

        $joinCondition = array(
            'e.entity_id = wp.product_id',
            'wp.website_id = :website_id'
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $joinCondition[] = 'e.child_id = wp.product_id';
        }
        $bind   = array('website_id'    => $websiteId);
        $select = $adapter->select()
            ->from(array('e' => $this->getFlatTableName($storeId)), null)
            ->joinLeft(
                array('wp' => $this->getTable('catalog/product_website')),
                implode(' AND ', $joinCondition),
                array());
        if ($productIds !== null) {
            $condition = array(
                $adapter->quoteInto('e.entity_id IN(?)', $productIds)
            );
            if ($this->getFlatHelper()->isAddChildData()) {
                $condition[] = $adapter->quoteInto('e.child_id IN(?)', $productIds);
            }
            $select->where(implode(' OR ', $condition));
        }

        $sql = $select->deleteFromSelect('e');
        $adapter->query($sql, $bind);

        return $this;
    }

    /**
     * Update attribute flat data
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $storeId
     * @param int|array $productIds update only product(s)
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function updateAttribute($attribute, $storeId, $productIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter       = $this->_getWriteAdapter();
        $flatTableName = $this->getFlatTableName($storeId);
        $describe      = $adapter->describeTable($flatTableName);

        if ($attribute->getBackend()->getType() == 'static') {
            if (!isset($describe[$attribute->getAttributeCode()])) {
                return $this;
            }

            $select = $adapter->select()
                ->join(
                    array('main_table' => $this->getTable('catalog/product')),
                    'main_table.entity_id = e.entity_id',
                    array($attribute->getAttributeCode() => 'main_table.' . $attribute->getAttributeCode())
                );
            if ($this->getFlatHelper()->isAddChildData()) {
                $select->where('e.is_child = ?', 0);
            }
            if ($productIds !== null) {
                $select->where('main_table.entity_id IN(?)', $productIds);
            }
            $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
            $adapter->query($sql);
        } else {
            $columns = $attribute->getFlatColumns();
            if (!$columns) {
                return $this;
            }
            foreach (array_keys($columns) as $columnName) {
                if (!isset($describe[$columnName])) {
                    return $this;
                }
            }

            $select = $attribute->getFlatUpdateSelect($storeId);
            if ($select instanceof Varien_Db_Select) {
                if ($productIds !== null) {
                    $select->where('e.entity_id IN(?)', $productIds);
                }
                $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Update non static EAV attributes flat data
     *
     * @param int $storeId
     * @param int|array $productIds update only product(s)
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function updateEavAttributes($storeId, $productIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        foreach ($this->getAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() != 'static') {
                $this->updateAttribute($attribute, $storeId, $productIds);
            }
        }
        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $storeId
     */
    public function updateEventAttributes($storeId = null)
    {
        Mage::dispatchEvent('catalog_product_flat_rebuild', array(
            'store_id' => $storeId,
            'table'    => $this->getFlatTableName($storeId)
        ));
    }

    /**
     * Retrieve Product Type Instances
     * as key - type code, value - instance model
     *
     * @return array
     */
    public function getProductTypeInstances()
    {
        if ($this->_productTypes === null) {
            $this->_productTypes = array();
            $productEmulator     = new Varien_Object();

            foreach (array_keys(Mage_Catalog_Model_Product_Type::getTypes()) as $typeId) {
                $productEmulator->setTypeId($typeId);
                $this->_productTypes[$typeId] = Mage::getSingleton('catalog/product_type')
                    ->factory($productEmulator);
            }
        }
        return $this->_productTypes;
    }

    /**
     * Update relation products
     *
     * @param int $storeId
     * @param int|array $productIds Update child product(s) only
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function updateRelationProducts($storeId, $productIds = null)
    {
        if (!$this->getFlatHelper()->isAddChildData() || !$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        foreach ($this->getProductTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                && $relation->getTable()
                && $relation->getParentFieldName()
                && $relation->getChildFieldName()
            ) {
                $columns    = $this->getFlatColumns();
                $fieldList  = array_keys($columns);
                unset($columns['entity_id']);
                unset($columns['child_id']);
                unset($columns['is_child']);

                $select = $adapter->select()
                    ->from(
                        array('t' => $this->getTable($relation->getTable())),
                        array($relation->getParentFieldName(), $relation->getChildFieldName(), new Zend_Db_Expr('1')))
                    ->join(
                        array('e' => $this->getFlatTableName($storeId)),
                        "e.entity_id = t.{$relation->getChildFieldName()}",
                        array_keys($columns)
                    );
                if ($relation->getWhere() !== null) {
                    $select->where($relation->getWhere());
                }
                if ($productIds !== null) {
                    $cond = array(
                        $adapter->quoteInto("{$relation->getChildFieldName()} IN(?)", $productIds),
                        $adapter->quoteInto("{$relation->getParentFieldName()} IN(?)", $productIds)
                    );

                    $select->where(implode(' OR ', $cond));
                }
                $sql = $select->insertFromSelect($this->getFlatTableName($storeId), $fieldList);
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Update children data from parent
     *
     * @param int $storeId
     * @param int|array $productIds
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function updateChildrenDataFromParent($storeId, $productIds = null)
    {
        if (!$this->getFlatHelper()->isAddChildData() || !$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        foreach (array_keys($this->getFlatColumns()) as $columnName) {
            if ($columnName == 'entity_id' || $columnName == 'child_id' || $columnName == 'is_child') {
                continue;
            }
            $select->columns(array($columnName => new Zend_Db_Expr('t1.' . $columnName)));
        }
        $select
            ->joinLeft(
                array('t1' => $this->getFlatTableName($storeId)),
                $adapter->quoteInto('t2.child_id = t1.entity_id AND t1.is_child = ?', 0),
                array())
            ->where('t2.is_child = ?', 1);

        if ($productIds !== null) {
            $select->where('t2.child_id IN(?)', $productIds);
        }

        $sql = $select->crossUpdateFromSelect(array('t2' => $this->getFlatTableName($storeId)));
        $adapter->query($sql);

        return $this;
    }

    /**
     * Clean unused relation products
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function cleanRelationProducts($storeId)
    {
        if (!$this->getFlatHelper()->isAddChildData()) {
            return $this;
        }

        foreach ($this->getProductTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $adapter  = $this->_getWriteAdapter();
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                && $relation->getTable()
                && $relation->getParentFieldName()
                && $relation->getChildFieldName()
            ) {
                $select = $this->_getWriteAdapter()->select()
                    ->distinct(true)
                    ->from(
                        $this->getTable($relation->getTable()),
                        "{$relation->getParentFieldName()}"
                    );
                $joinLeftCond = array(
                    "e.entity_id = t.{$relation->getParentFieldName()}",
                    "e.child_id = t.{$relation->getChildFieldName()}"
                );
                if ($relation->getWhere() !== null) {
                    $select->where($relation->getWhere());
                    $joinLeftCond[] = $relation->getWhere();
                }

                $entitySelect = new Zend_Db_Expr($select->__toString());

                $select = $adapter->select()
                    ->from(array('e' => $this->getFlatTableName($storeId)), null)
                    ->joinLeft(
                        array('t' => $this->getTable($relation->getTable())),
                        implode(' AND ', $joinLeftCond),
                        array())
                    ->where('e.is_child = ?', 1)
                    ->where('e.entity_id IN(?)', $entitySelect)
                    ->where("t.{$relation->getChildFieldName()} IS NULL");

                $sql = $select->deleteFromSelect('e');
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Remove product data from flat
     *
     * @param int|array $productIds
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function removeProduct($productIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $cond = array(
            $adapter->quoteInto('entity_id IN(?)', $productIds)
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $cond[] = $adapter->quoteInto('child_id IN(?)', $productIds);
        }
        $cond = implode(' OR ', $cond);
        $adapter->delete($this->getFlatTableName($storeId), $cond);

        return $this;
    }

    /**
     * Remove children from parent product
     *
     * @param int|array $productIds
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function removeProductChildren($productIds, $storeId)
    {
        if (!$this->getFlatHelper()->isAddChildData()) {
            return $this;
        }
        $whereExpr = array(
            'entity_id IN(?)' => $productIds,
            'is_child = ?'    => 1
        );
        $this->_getWriteAdapter()->delete($this->getFlatTableName($storeId), $whereExpr);

        return $this;
    }

    /**
     * Update flat data for product
     *
     * @param int|array $productIds
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function updateProduct($productIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $this->saveProduct($productIds, $storeId);

        Mage::dispatchEvent('catalog_product_flat_update_product', array(
            'store_id'      => $storeId,
            'table'         => $this->getFlatTableName($storeId),
            'product_ids'   => $productIds
        ));

        return $this;
    }

    /**
     * Save product(s) data for store
     *
     * @param int|array $productIds
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function saveProduct($productIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $this->updateStaticAttributes($storeId, $productIds);
        $this->updateEavAttributes($storeId, $productIds);

        return $this;
    }

    /**
     * Delete flat table process
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Flat_Indexer
     */
    public function deleteFlatTable($storeId)
    {
        if ($this->_isFlatTableExists($storeId)) {
            $this->_getWriteAdapter()->dropTable($this->getFlatTableName($storeId));
        }

        return $this;
    }

    /**
     * Check is flat table for store exists
     *
     * @param int $storeId
     * @return bool
     */
    protected function _isFlatTableExists($storeId)
    {
        if (!isset($this->_existsFlatTables[$storeId])) {
            $tableName     = $this->getFlatTableName($storeId);
            $isTableExists = $this->_getWriteAdapter()->isTableExists($tableName);

            $this->_existsFlatTables[$storeId] = $isTableExists ? true : false;
        }

        return $this->_existsFlatTables[$storeId];
    }

    /**
     * Retrieve previous key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    protected function _arrayPrevKey(array $array, $key)
    {
        $prev = false;
        foreach (array_keys($array) as $k) {
            if ($k == $key) {
                return $prev;
            }
            $prev = $k;
        }
        return false;
    }

    /**
     * Retrieve next key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    protected function _arrayNextKey(array $array, $key)
    {
        $next = false;
        foreach (array_keys($array) as $k) {
            if ($next === true) {
                return $k;
            }
            if ($k == $key) {
                $next = true;
            }
        }
        return false;
    }
}
