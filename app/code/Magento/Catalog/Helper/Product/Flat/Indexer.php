<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product\Flat;

/**
 * Catalog Product Flat Indexer Helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 */
class Indexer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to list of attributes used for flat indexer
     */
    const XML_NODE_ATTRIBUTE_NODES = 'global/catalog/product/flat/attribute_groups';

    /**
     * Size of ids batch for reindex
     */
    const BATCH_SIZE = 500;

    /**
     * Resource instance
     *
     * @var \Magento\Framework\App\Resource
     */
    protected $_resource;

    /**
     * @var array
     */
    protected $_columns;

    /**
     * List of indexes uses in flat product table
     *
     * @var null|array
     */
    protected $_indexes;

    /**
     * Retrieve catalog product flat columns array in old format (used before MMDB support)
     *
     * @return array
     */
    protected $_attributes;

    /**
     * Required system attributes for preload
     *
     * @var array
     */
    protected $_systemAttributes = array('status', 'required_options', 'tax_class_id', 'weight');

    /**
     * EAV Config instance
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Catalog\Model\Attribute\Config
     */
    private $_attributeConfig;

    /**
     * @var array
     */
    protected $_attributeCodes;

    /**
     * @var int
     */
    protected $_entityTypeId;

    /**
     * @var \Magento\Catalog\Model\Resource\Config
     */
    protected $_catalogConfig;

    /**
     * @var array
     */
    protected $_flatAttributeGroups = array();

    /**
     * Config factory
     *
     * @var \Magento\Catalog\Model\Resource\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var bool
     */
    protected $_addFilterableAttrs;

    /**
     * @var bool
     */
    protected $_addChildData;

    /**
     * @var \Magento\Mview\View\Changelog
     */
    protected $_changelog;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Attribute\Config $attributeConfig
     * @param \Magento\Catalog\Model\Resource\ConfigFactory $configFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Mview\View\Changelog $changelog
     * @param bool $addFilterableAttrs
     * @param bool $addChildData
     * @param array $flatAttributeGroups
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Attribute\Config $attributeConfig,
        \Magento\Catalog\Model\Resource\ConfigFactory $configFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Mview\View\Changelog $changelog,
        $addFilterableAttrs = false,
        $addChildData = false,
        $flatAttributeGroups = array()
    ) {
        $this->_configFactory = $configFactory;
        $this->_resource = $resource;
        $this->_eavConfig = $eavConfig;
        $this->_attributeConfig = $attributeConfig;
        $this->_attributeFactory = $attributeFactory;
        $this->_flatAttributeGroups = $flatAttributeGroups;
        $this->_storeManager = $storeManager;
        $this->_changelog = $changelog;
        $this->_addFilterableAttrs = $addFilterableAttrs;
        $this->_addChildData = $addChildData;
        parent::__construct($context);
    }

    /**
     * Retrieve catalog product flat columns array in DDL format
     *
     * @return array
     */
    public function getFlatColumnsDdlDefinition()
    {
        $columns = array();
        $columns['entity_id'] = array(
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => null,
            'unsigned' => true,
            'nullable' => false,
            'default' => false,
            'primary' => true,
            'comment' => 'Entity Id'
        );
        if ($this->isAddChildData()) {
            $columns['child_id'] = array(
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => null,
                'unsigned' => true,
                'nullable' => true,
                'default' => null,
                'primary' => true,
                'comment' => 'Child Id'
            );
            $columns['is_child'] = array(
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 1,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Checks If Entity Is Child'
            );
        }
        $columns['attribute_set_id'] = array(
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length' => 5,
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Attribute Set Id'
        );
        $columns['type_id'] = array(
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 32,
            'unsigned' => false,
            'nullable' => false,
            'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE,
            'comment' => 'Type Id'
        );
        return $columns;
    }

    /**
     * Check whether filterable attributes should be added
     *
     * @return bool
     */
    public function isAddFilterableAttributes()
    {
        return $this->_addFilterableAttrs;
    }

    /**
     * Check whether child data should be added
     *
     * @return bool
     */
    public function isAddChildData()
    {
        return $this->_addChildData;
    }

    /**
     * Retrieve catalog product flat table columns array
     *
     * @return array
     */
    public function getFlatColumns()
    {
        if ($this->_columns === null) {
            $this->_columns = $this->getFlatColumnsDdlDefinition();
            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
                $columns = $attribute->setFlatAddFilterableAttributes(
                    $this->isAddFilterableAttributes()
                )->setFlatAddChildData(
                    $this->isAddChildData()
                )->getFlatColumns();
                if ($columns !== null) {
                    $this->_columns = array_merge($this->_columns, $columns);
                }
            }
        }
        return $this->_columns;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return \Magento\Catalog\Model\Product::ENTITY;
    }

    /**
     * Retrieve Catalog Entity Type Id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = $this->_configFactory->create()->getEntityTypeId();
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
            $attributeCodes = $this->getAttributeCodes();
            $entity = $this->_eavConfig->getEntityType($this->getEntityType())->getEntity();

            foreach ($attributeCodes as $attributeCode) {
                $attribute = $this->_eavConfig->getAttribute(
                    $this->getEntityType(),
                    $attributeCode
                )->setEntity(
                    $entity
                );
                try {
                    // check if exists source and backend model.
                    // To prevent exception when some module was disabled
                    $attribute->usesSource() && $attribute->getSource();
                    $attribute->getBackend();
                    $this->_attributes[$attributeCode] = $attribute;
                } catch (\Exception $e) {
                    $this->_logger->logException($e);
                }
            }
        }
        return $this->_attributes;
    }

    /**
     * Retrieve attribute codes using for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        if ($this->_attributeCodes === null) {
            $adapter = $this->_resource->getConnection('read');
            $this->_attributeCodes = array();

            foreach ($this->_flatAttributeGroups as $attributeGroupName) {
                $attributes = $this->_attributeConfig->getAttributeNames($attributeGroupName);
                $this->_systemAttributes = array_unique(array_merge($attributes, $this->_systemAttributes));
            }

            $bind = array(
                'backend_type' => \Magento\Eav\Model\Entity\Attribute\AbstractAttribute::TYPE_STATIC,
                'entity_type_id' => $this->getEntityTypeId()
            );

            $select = $adapter->select()->from(
                array('main_table' => $this->getTable('eav_attribute'))
            )->join(
                array('additional_table' => $this->getTable('catalog_eav_attribute')),
                'additional_table.attribute_id = main_table.attribute_id'
            )->where(
                'main_table.entity_type_id = :entity_type_id'
            );
            $whereCondition = array(
                'main_table.backend_type = :backend_type',
                $adapter->quoteInto('additional_table.is_used_for_promo_rules = ?', 1),
                $adapter->quoteInto('additional_table.used_in_product_listing = ?', 1),
                $adapter->quoteInto('additional_table.used_for_sort_by = ?', 1),
                $adapter->quoteInto('main_table.attribute_code IN(?)', $this->_systemAttributes)
            );
            if ($this->isAddFilterableAttributes()) {
                $whereCondition[] = $adapter->quoteInto('additional_table.is_filterable > ?', 0);
            }

            $select->where(implode(' OR ', $whereCondition));
            $attributesData = $adapter->fetchAll($select, $bind);
            $this->_eavConfig->importAttributesData($this->getEntityType(), $attributesData);

            foreach ($attributesData as $data) {
                $this->_attributeCodes[$data['attribute_id']] = $data['attribute_code'];
            }
            unset($attributesData);
        }
        return $this->_attributeCodes;
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
            if ($this->isAddChildData()) {
                $this->_indexes['PRIMARY'] = array(
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id', 'child_id')
                );
                $this->_indexes['IDX_CHILD'] = array(
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                    'fields' => array('child_id')
                );
                $this->_indexes['IDX_IS_CHILD'] = array(
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                    'fields' => array('entity_id', 'is_child')
                );
            } else {
                $this->_indexes['PRIMARY'] = array(
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id')
                );
            }
            $this->_indexes['IDX_TYPE_ID'] = array(
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                'fields' => array('type_id')
            );
            $this->_indexes['IDX_ATTRIBUTE_SET'] = array(
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                'fields' => array('attribute_set_id')
            );

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $indexes = $attribute->setFlatAddFilterableAttributes(
                    $this->isAddFilterableAttributes()
                )->setFlatAddChildData(
                    $this->isAddChildData()
                )->getFlatIndexes();
                if ($indexes !== null) {
                    $this->_indexes = array_merge($this->_indexes, $indexes);
                }
            }
        }
        return $this->_indexes;
    }

    /**
     * Get table structure for temporary eav tables
     *
     * @param array $attributes
     * @return array
     */
    public function getTablesStructure(array $attributes)
    {
        $eavAttributes = array();
        $flatColumnsList = $this->getFlatColumns();
        /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
        foreach ($attributes as $attribute) {
            $eavTable = $attribute->getBackend()->getTable();
            $attributeCode = $attribute->getAttributeCode();
            if (isset($flatColumnsList[$attributeCode])) {
                $eavAttributes[$eavTable][$attributeCode] = $attribute;
            }
        }
        return $eavAttributes;
    }

    /**
     * Returns table name
     *
     * @param string|array $name
     * @return string
     */
    public function getTable($name)
    {
        return $this->_resource->getTableName($name);
    }

    /**
     * Retrieve Catalog Product Flat Table name
     *
     * @param int $storeId
     * @return string
     */
    public function getFlatTableName($storeId)
    {
        return sprintf('%s_%s', $this->getTable('catalog_product_flat'), $storeId);
    }

    /**
     * Retrieve loaded attribute by code
     *
     * @param string $attributeCode
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (!isset($attributes[$attributeCode])) {
            $attribute = $this->_attributeFactory->create();
            $attribute->loadByCode($this->getEntityTypeId(), $attributeCode);
            if (!$attribute->getId()) {
                throw new \Magento\Framework\Model\Exception(__('Invalid attribute %1', $attributeCode));
            }
            $entity = $this->_eavConfig->getEntityType($this->getEntityType())->getEntity();
            $attribute->setEntity($entity);
            return $attribute;
        }
        return $attributes[$attributeCode];
    }

    /**
     * Delete all product flat tables for not existing stores
     *
     * @return void
     */
    public function deleteAbandonedStoreFlatTables()
    {
        $connection = $this->_resource->getConnection('write');
        $existentTables = $connection->getTables($connection->getTableName('catalog_product_flat_%'));
        $this->_changelog->setViewId('catalog_product_flat');
        foreach ($existentTables as $key => $tableName) {
            if ($this->_changelog->getName() == $tableName) {
                unset($existentTables[$key]);
            }
        }
        $actualStoreTables = array();
        foreach ($this->_storeManager->getStores() as $store) {
            $actualStoreTables[] = $this->getFlatTableName($store->getId());
        }

        $tablesToDelete = array_diff($existentTables, $actualStoreTables);

        foreach ($tablesToDelete as $table) {
            $connection->dropTable($table);
        }
    }
}
