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
 * Entity type model
 *
 * @method Magento_Eav_Model_Resource_Entity_Type _getResource()
 * @method Magento_Eav_Model_Resource_Entity_Type getResource()
 * @method Magento_Eav_Model_Entity_Type setEntityTypeCode(string $value)
 * @method string getEntityModel()
 * @method Magento_Eav_Model_Entity_Type setEntityModel(string $value)
 * @method Magento_Eav_Model_Entity_Type setAttributeModel(string $value)
 * @method Magento_Eav_Model_Entity_Type setEntityTable(string $value)
 * @method Magento_Eav_Model_Entity_Type setValueTablePrefix(string $value)
 * @method Magento_Eav_Model_Entity_Type setEntityIdField(string $value)
 * @method int getIsDataSharing()
 * @method Magento_Eav_Model_Entity_Type setIsDataSharing(int $value)
 * @method string getDataSharingKey()
 * @method Magento_Eav_Model_Entity_Type setDataSharingKey(string $value)
 * @method Magento_Eav_Model_Entity_Type setDefaultAttributeSetId(int $value)
 * @method string getIncrementModel()
 * @method Magento_Eav_Model_Entity_Type setIncrementModel(string $value)
 * @method int getIncrementPerStore()
 * @method Magento_Eav_Model_Entity_Type setIncrementPerStore(int $value)
 * @method int getIncrementPadLength()
 * @method Magento_Eav_Model_Entity_Type setIncrementPadLength(int $value)
 * @method string getIncrementPadChar()
 * @method Magento_Eav_Model_Entity_Type setIncrementPadChar(string $value)
 * @method string getAdditionalAttributeTable()
 * @method Magento_Eav_Model_Entity_Type setAdditionalAttributeTable(string $value)
 * @method Magento_Eav_Model_Entity_Type setEntityAttributeCollection(string $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Type extends Magento_Core_Model_Abstract
{
    /**
     * Collection of attributes
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected $_attributes;

    /**
     * Array of attributes
     *
     * @var array
     */
    protected $_attributesBySet             = array();

    /**
     * Collection of sets
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection
     */
    protected $_sets;

    /**
     * @var Magento_Eav_Model_Entity_AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @var Magento_Eav_Model_Entity_Attribute_SetFactory
     */
    protected $_attSetFactory;

    /***
     * @var Magento_Eav_Model_Entity_StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var Magento_Eav_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Eav_Model_Entity_AttributeFactory $attributeFactory
     * @param Magento_Eav_Model_Entity_Attribute_SetFactory $attSetFactory
     * @param Magento_Eav_Model_Entity_StoreFactory $storeFactory
     * @param Magento_Eav_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Eav_Model_Entity_AttributeFactory $attributeFactory,
        Magento_Eav_Model_Entity_Attribute_SetFactory $attSetFactory,
        Magento_Eav_Model_Entity_StoreFactory $storeFactory,
        Magento_Eav_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_attributeFactory = $attributeFactory;
        $this->_attSetFactory = $attSetFactory;
        $this->_storeFactory = $storeFactory;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Entity_Type');
    }

    /**
     * Load type by code
     *
     * @param string $code
     * @return Magento_Eav_Model_Entity_Type
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Retrieve entity type attributes collection
     *
     * @param   int $setId
     * @return  Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributeCollection($setId = null)
    {
        if ($setId === null) {
            if ($this->_attributes === null) {
                $this->_attributes = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this);
            }
            $collection = $this->_attributes;
        } else {
            if (!isset($this->_attributesBySet[$setId])) {
                $this->_attributesBySet[$setId] = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this)
                    ->setAttributeSetFilter($setId);
            }
            $collection = $this->_attributesBySet[$setId];
        }

        return $collection;
    }

    /**
     * Init and retreive attribute collection
     *
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        $collection = $this->_attributeFactory->create()->getCollection();
        $objectsModel = $this->getAttributeModel();
        if ($objectsModel) {
            $collection->setModel($objectsModel);
        }

        return $collection;
    }

    /**
     * Retrieve entity tpe sets collection
     *
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection
     */
    public function getAttributeSetCollection()
    {
        if (empty($this->_sets)) {
            $this->_sets = $this->_attSetFactory->create()->getResourceCollection()
                ->setEntityTypeFilter($this->getId());
        }
        return $this->_sets;
    }

    /**
     * Retreive new incrementId
     *
     * @param int $storeId
     * @return string
     */
    public function fetchNewIncrementId($storeId = null)
    {
        if (!$this->getIncrementModel()) {
            return false;
        }

        if (!$this->getIncrementPerStore() || ($storeId === null)) {
            /**
             * store_id null we can have for entity from removed store
             */
            $storeId = 0;
        }

        // Start transaction to run SELECT ... FOR UPDATE
        $this->_getResource()->beginTransaction();

        $entityStoreConfig = $this->_storeFactory->create()
            ->loadByEntityStore($this->getId(), $storeId);

        if (!$entityStoreConfig->getId()) {
            $entityStoreConfig
                ->setEntityTypeId($this->getId())
                ->setStoreId($storeId)
                ->setIncrementPrefix($storeId)
                ->save();
        }

        $incrementInstance = $this->_helperFactory->create($this->getIncrementModel())
            ->setPrefix($entityStoreConfig->getIncrementPrefix())
            ->setPadLength($this->getIncrementPadLength())
            ->setPadChar($this->getIncrementPadChar())
            ->setLastId($entityStoreConfig->getIncrementLastId())
            ->setEntityTypeId($entityStoreConfig->getEntityTypeId())
            ->setStoreId($entityStoreConfig->getStoreId());

        /**
         * do read lock on eav/entity_store to solve potential timing issues
         * (most probably already done by beginTransaction of entity save)
         */
        $incrementId = $incrementInstance->getNextId();
        $entityStoreConfig->setIncrementLastId($incrementId);
        $entityStoreConfig->save();

        // Commit increment_last_id changes
        $this->_getResource()->commit();

        return $incrementId;
    }

    /**
     * Retreive entity id field
     *
     * @return string|null
     */
    public function getEntityIdField()
    {
        return isset($this->_data['entity_id_field']) ? $this->_data['entity_id_field'] : null;
    }

    /**
     * Retreive entity table name
     *
     * @return string|null
     */
    public function getEntityTable()
    {
        return isset($this->_data['entity_table']) ? $this->_data['entity_table'] : null;
    }

    /**
     * Retrieve entity table prefix name
     *
     * @return string
     */
    public function getValueTablePrefix()
    {
        $prefix = $this->getEntityTablePrefix();
        if ($prefix) {
            return $this->getResource()->getTable($prefix);
        }

        return null;
    }

    /**
     * Retrieve entity table prefix
     *
     * @return string
     */
    public function getEntityTablePrefix()
    {
        $tablePrefix = trim($this->_data['value_table_prefix']);

        if (empty($tablePrefix)) {
            $tablePrefix = $this->getEntityTable();
        }

        return $tablePrefix;
    }

    /**
     * Get default attribute set identifier for etity type
     *
     * @return string|null
     */
    public function getDefaultAttributeSetId()
    {
        return isset($this->_data['default_attribute_set_id']) ? $this->_data['default_attribute_set_id'] : null;
    }

    /**
     * Retreive entity type id
     *
     * @return string|null
     */
    public function getEntityTypeId()
    {
        return isset($this->_data['entity_type_id']) ? $this->_data['entity_type_id'] : null;
    }

    /**
     * Retreive entity type code
     *
     * @return string|null
     */
    public function getEntityTypeCode()
    {
        return isset($this->_data['entity_type_code']) ? $this->_data['entity_type_code'] : null;
    }

    /**
     * Retreive attribute codes
     *
     * @return array|null
     */
    public function getAttributeCodes()
    {
        return isset($this->_data['attribute_codes']) ? $this->_data['attribute_codes'] : null;
    }

    /**
     * Get attribute model code for entity type
     *
     * @return string
     */
    public function getAttributeModel()
    {
        if (empty($this->_data['attribute_model'])) {
            return Magento_Eav_Model_Entity::DEFAULT_ATTRIBUTE_MODEL;
        }

        return $this->_data['attribute_model'];
    }

    /**
     * Retreive resource entity object
     *
     * @return Magento_Core_Model_Resource_Abstract
     */
    public function getEntity()
    {
        return $this->_helperFactory->create($this->_data['entity_model']);
    }

    /**
     * Return attribute collection. If not specify return default
     *
     * @return string
     */
    public function getEntityAttributeCollection()
    {
        $collection = $this->_getData('entity_attribute_collection');
        if ($collection) {
            return $collection;
        }
        return 'Magento_Eav_Model_Resource_Entity_Attribute_Collection';
    }
}
