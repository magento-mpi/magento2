<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Entity/Attribute/Model - attribute abstract
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Eav_Model_Entity_Attribute_Abstract
    extends Magento_Core_Model_Abstract
    implements Magento_Eav_Model_Entity_Attribute_Interface
{
    const TYPE_STATIC = 'static';

    /**
     * Attribute name
     *
     * @var string
     */
    protected $_name;

    /**
     * Entity instance
     *
     * @var Magento_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Backend instance
     *
     * @var Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    protected $_backend;

    /**
     * Frontend instance
     *
     * @var Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    protected $_frontend;

    /**
     * Source instance
     *
     * @var Magento_Eav_Model_Entity_Attribute_Source_Abstract
     */
    protected $_source;

    /**
     * Attribute id cache
     *
     * @var array
     */
    protected $_attributeIdCache            = array();

    /**
     * Attribute data table name
     *
     * @var string
     */
    protected $_dataTable                   = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_Eav_Model_Entity_TypeFactory
     */
    protected $_eavTypeFactory;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Eav_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * @var Magento_Eav_Model_Factory_Helper
     */
    protected $_factoryHelper;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Eav_Model_Entity_TypeFactory $eavTypeFactory
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Eav_Model_Factory_Helper $factoryHelper
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Eav_Model_Entity_TypeFactory $eavTypeFactory,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Eav_Model_Factory_Helper $factoryHelper,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_coreData = $coreData;
        $this->_eavConfig = $eavConfig;
        $this->_eavTypeFactory = $eavTypeFactory;
        $this->_storeManager = $storeManager;
        $this->_resourceHelper = $resourceHelper;
        $this->_factoryHelper = $factoryHelper;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Entity_Attribute');
    }

    /**
     * Load attribute data by code
     *
     * @param  mixed $entityType
     * @param  string $code
     * @return Magento_Eav_Model_Entity_Attribute_Abstract
     * @throws Magento_Core_Exception
     */
    public function loadByCode($entityType, $code)
    {
        Magento_Profiler::start('load_by_code');
        if (is_numeric($entityType)) {
            $entityTypeId = $entityType;
        } elseif (is_string($entityType)) {
            $entityType = $this->_eavTypeFactory->create()->loadByCode($entityType);
        }
        if ($entityType instanceof Magento_Eav_Model_Entity_Type) {
            $entityTypeId = $entityType->getId();
        }
        if (empty($entityTypeId)) {
            throw new Magento_Eav_Exception(__('Invalid entity supplied'));
        }
        $this->_getResource()->loadByCode($this, $entityTypeId, $code);
        $this->_afterLoad();
        Magento_Profiler::stop('load_by_code');
        return $this;
    }

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('attribute_code');
    }

    /**
     * Specify attribute identifier
     *
     * @param   int $data
     * @return  Magento_Eav_Model_Entity_Attribute_Abstract
     */
    public function setAttributeId($data)
    {
        $this->_data['attribute_id'] = $data;
        return $this;
    }

    /**
     * Get attribute identifuer
     *
     * @return int | null
     */
    public function getAttributeId()
    {
        return $this->_getData('attribute_id');
    }

    public function setAttributeCode($data)
    {
        return $this->setData('attribute_code', $data);
    }

    public function getAttributeCode()
    {
        return $this->_getData('attribute_code');
    }

    public function setAttributeModel($data)
    {
        return $this->setData('attribute_model', $data);
    }

    public function getAttributeModel()
    {
        return $this->_getData('attribute_model');
    }

    public function setBackendType($data)
    {
        return $this->setData('backend_type', $data);
    }

    public function getBackendType()
    {
        return $this->_getData('backend_type');
    }

    public function setBackendModel($data)
    {
        return $this->setData('backend_model', $data);
    }

    public function getBackendModel()
    {
        return $this->_getData('backend_model');
    }

    public function setBackendTable($data)
    {
        return $this->setData('backend_table', $data);
    }

    public function getIsVisibleOnFront()
    {
        return $this->_getData('is_visible_on_front');
    }

    public function getDefaultValue()
    {
        return $this->_getData('default_value');
    }

    public function getAttributeSetId()
    {
        return $this->_getData('attribute_set_id');
    }

    public function setAttributeSetId($id)
    {
        $this->_data['attribute_set_id'] = $id;
        return $this;
    }

    public function getEntityTypeId()
    {
        return $this->_getData('entity_type_id');
    }

    public function setEntityTypeId($id)
    {
        $this->_data['entity_type_id'] = $id;
        return $this;
    }

    public function setEntityType($type)
    {
        $this->setData('entity_type', $type);
        return $this;
    }

    /**
     * Get attribute alias as "entity_type/attribute_code"
     *
     * @param Magento_Eav_Model_Entity_Abstract $entity exclude this entity
     * @return string
     */
    public function getAlias($entity = null)
    {
        $alias = '';
        if (($entity === null) || ($entity->getType() !== $this->getEntity()->getType())) {
            $alias .= $this->getEntity()->getType() . '/';
        }
        $alias .= $this->getAttributeCode();

        return  $alias;
    }

    /**
     * Set attribute name
     *
     * @param   string $name
     * @return  Magento_Eav_Model_Entity_Attribute_Abstract
     */
    public function setName($name)
    {
        return $this->setData('attribute_code', $name);
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->_eavConfig->getEntityType($this->getEntityTypeId());
    }

    /**
     * Set attribute entity instance
     *
     * @param Magento_Eav_Model_Entity_Abstract $entity
     * @return Magento_Eav_Model_Entity_Attribute_Abstract
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Retrieve entity instance
     *
     * @return Magento_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        if (!$this->_entity) {
            $this->_entity = $this->getEntityType();
        }
        return $this->_entity;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getEntity()->getValueEntityIdField();
    }

    /**
     * Retrieve backend instance
     *
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     * @throws Magento_Core_Exception
     */
    public function getBackend()
    {
        if (empty($this->_backend)) {
            if (!$this->getBackendModel()) {
                $this->setBackendModel($this->_getDefaultBackendModel());
            }
            $backend = $this->_factoryHelper->create($this->getBackendModel());
            if (!$backend) {
                throw new Magento_Eav_Exception(__('Invalid backend model specified: ' . $this->getBackendModel()));
            }
            $this->_backend = $backend->setAttribute($this);
        }

        return $this->_backend;
    }

    /**
     * Retrieve frontend instance
     *
     * @return Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function getFrontend()
    {
        if (empty($this->_frontend)) {
            if (!$this->getFrontendModel()) {
                $this->setFrontendModel($this->_getDefaultFrontendModel());
            }
            $this->_frontend = $this->_factoryHelper->create($this->getFrontendModel())
                ->setAttribute($this);
        }

        return $this->_frontend;
    }

    /**
     * Retrieve source instance
     *
     * @return Magento_Eav_Model_Entity_Attribute_Source_Abstract
     * @throws Magento_Core_Exception
     */
    public function getSource()
    {
        if (empty($this->_source)) {
            if (!$this->getSourceModel()) {
                $this->setSourceModel($this->_getDefaultSourceModel());
            }
            $source = $this->_factoryHelper->create($this->getSourceModel());
            if (!$source) {
                throw new Magento_Eav_Exception(
                    __('Source model "%1" not found for attribute "%2"',$this->getSourceModel(), $this->getAttributeCode())
                );
            }
            $this->_source = $source->setAttribute($this);
        }
        return $this->_source;
    }

    /**
     * Whether possible attribute values are retrieved from finite source
     *
     * @return bool
     */
    public function usesSource()
    {
        $input = $this->getFrontendInput();
        return $input === 'select' || $input === 'multiselect' || $this->_getData('source_model') != '';
    }

    protected function _getDefaultBackendModel()
    {
        return Magento_Eav_Model_Entity::DEFAULT_BACKEND_MODEL;
    }

    protected function _getDefaultFrontendModel()
    {
        return Magento_Eav_Model_Entity::DEFAULT_FRONTEND_MODEL;
    }

    protected function _getDefaultSourceModel()
    {
        return $this->getEntity()->getDefaultAttributeSourceModel();
    }

    public function isValueEmpty($value)
    {
        $attrType = $this->getBackend()->getType();
        $isEmpty = is_array($value)
            || ($value === null)
            || $value === false && $attrType != 'int'
            || $value === '' && ($attrType == 'int' || $attrType == 'decimal' || $attrType == 'datetime');

        return $isEmpty;
    }

    /**
     * Check if attribute in specified set
     *
     * @param int|array $setId
     * @return boolean
     */
    public function isInSet($setId)
    {
        if (!$this->hasAttributeSetInfo()) {
            return true;
        }

        if (is_array($setId)
            && count(array_intersect($setId, array_keys($this->getAttributeSetInfo())))) {
            return true;
        }

        if (!is_array($setId)
            && array_key_exists($setId, $this->getAttributeSetInfo())) {
            return true;
        }

        return false;
    }

    /**
     * Check if attribute in specified group
     *
     * @param int $setId
     * @param int $groupId
     * @return boolean
     */
    public function isInGroup($setId, $groupId)
    {
        $dataPath = sprintf('attribute_set_info/%s/group_id', $setId);
        if ($this->isInSet($setId) && $this->getData($dataPath) == $groupId) {
            return true;
        }

        return false;
    }

    /**
     * Return attribute id
     *
     * @param string $entityType
     * @param string $code
     * @return int
     */
    public function getIdByCode($entityType, $code)
    {
        $k = "{$entityType}|{$code}";
        if (!isset($this->_attributeIdCache[$k])) {
            $this->_attributeIdCache[$k] = $this->getResource()->getIdByCode($entityType, $code);
        }
        return $this->_attributeIdCache[$k];
    }

    /**
     * Check if attribute is static
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->getBackendType() == self::TYPE_STATIC || $this->getBackendType() == '';
    }

    /**
     * Get attribute backend table name
     *
     * @return string
     */
    public function getBackendTable()
    {
        if ($this->_dataTable === null) {
            if ($this->isStatic()) {
                $this->_dataTable = $this->getEntityType()->getValueTablePrefix();
            } else {
                $backendTable = trim($this->_getData('backend_table'));
                if (empty($backendTable)) {
                    $entityTable  = array($this->getEntity()->getEntityTablePrefix(), $this->getBackendType());
                    $backendTable = $this->getResource()->getTable($entityTable);
                }
                $this->_dataTable = $backendTable;
            }
        }
        return $this->_dataTable;
    }

    /**
     * Retrieve flat columns definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        // If source model exists - get definition from it
        if ($this->usesSource() && $this->getBackendType() != self::TYPE_STATIC) {
            return $this->getSource()->getFlatColums();
        }

        if ($this->_coreData->useDbCompatibleMode()) {
            return $this->_getFlatColumnsOldDefinition();
        } else {
            return $this->_getFlatColumnsDdlDefinition();
        }
    }

    /**
     * Retrieve flat columns DDL definition
     *
     * @return array
     */
    public function _getFlatColumnsDdlDefinition()
    {
        $columns = array();
        switch ($this->getBackendType()) {
            case 'static':
                $describe = $this->_getResource()->describeTable($this->getBackend()->getTable());
                if (!isset($describe[$this->getAttributeCode()])) {
                    break;
                }
                $prop = $describe[$this->getAttributeCode()];
                $type = $prop['DATA_TYPE'];
                $size = ($prop['LENGTH'] ? $prop['LENGTH'] : null);

                $columns[$this->getAttributeCode()] = array(
                    'type'      => $this->_resourceHelper->getDdlTypeByColumnType($type),
                    'length'    => $size,
                    'unsigned'  => $prop['UNSIGNED'] ? true: false,
                    'nullable'   => $prop['NULLABLE'],
                    'default'   => $prop['DEFAULT'],
                    'extra'     => null
                );
                break;
            case 'datetime':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_DATETIME,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'decimal':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'int':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_INTEGER,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'text':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                    'length'    => Magento_DB_Ddl_Table::MAX_TEXT_SIZE
                );
                break;
            case 'varchar':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
                    'length'    => '255',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
        }

        return $columns;
    }

    /**
     * Retrieve flat columns definition in old format (before MMDB support)
     * Used in database compatible mode
     *
     * @return array
     */
    protected function _getFlatColumnsOldDefinition() {
        $columns = array();
        switch ($this->getBackendType()) {
            case 'static':
                $describe = $this->_getResource()->describeTable($this->getBackend()->getTable());
                if (!isset($describe[$this->getAttributeCode()])) {
                    break;
                }
                $prop = $describe[$this->getAttributeCode()];
                $columns[$this->getAttributeCode()] = array(
                    'type'      => $prop['DATA_TYPE'] . ($prop['LENGTH'] ? "({$prop['LENGTH']})" : ""),
                    'unsigned'  => $prop['UNSIGNED'] ? true: false,
                    'is_null'   => $prop['NULLABLE'],
                    'default'   => $prop['DEFAULT'],
                    'extra'     => null
                );
                break;
            case 'datetime':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => 'datetime',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'decimal':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => 'decimal(12,4)',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'int':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => 'int',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'text':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => 'text',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
            case 'varchar':
                $columns[$this->getAttributeCode()] = array(
                    'type'      => 'varchar(255)',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null
                );
                break;
        }
        return $columns;
    }

    /**
     * Retrieve index data for flat table
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $condition = $this->getUsedForSortBy();
        if ($this->getFlatAddFilterableAttributes()) {
            $condition = $condition || $this->getIsFilterable();
        }

        if ($condition) {
            if ($this->usesSource() && $this->getBackendType() != self::TYPE_STATIC) {
                return $this->getSource()->getFlatIndexes();
            }
            $indexes = array();

            switch ($this->getBackendType()) {
                case 'static':
                    $describe = $this->_getResource()
                        ->describeTable($this->getBackend()->getTable());
                    if (!isset($describe[$this->getAttributeCode()])) {
                        break;
                    }
                    $indexDataTypes = array(
                        'varchar',
                        'varbinary',
                        'char',
                        'date',
                        'datetime',
                        'timestamp',
                        'time',
                        'year',
                        'enum',
                        'set',
                        'bit',
                        'bool',
                        'tinyint',
                        'smallint',
                        'mediumint',
                        'int',
                        'bigint',
                        'float',
                        'double',
                        'decimal',
                    );
                    $prop = $describe[$this->getAttributeCode()];
                    if (in_array($prop['DATA_TYPE'], $indexDataTypes)) {
                        $indexName = 'IDX_' . strtoupper($this->getAttributeCode());
                        $indexes[$indexName] = array(
                            'type'      => 'index',
                            'fields'    => array($this->getAttributeCode())
                        );
                    }

                    break;
                case 'datetime':
                case 'decimal':
                case 'int':
                case 'varchar':
                    $indexName = 'IDX_' . strtoupper($this->getAttributeCode());
                    $indexes[$indexName] = array(
                        'type'      => 'index',
                        'fields'    => array($this->getAttributeCode())
                    );
                    break;
            }

            return $indexes;
        }

        return array();
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Magento_DB_Select
     */
    public function getFlatUpdateSelect($store = null) {
        if ($store === null) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->getFlatUpdateSelect($store->getId());
            }
            return $this;
        }

        if ($this->getBackendType() == self::TYPE_STATIC) {
            return null;
        }

        if ($this->usesSource()) {
            return $this->getSource()->getFlatUpdateSelect($store);
        }
        return $this->_getResource()->getFlatUpdateSelect($this, $store);
    }
}
