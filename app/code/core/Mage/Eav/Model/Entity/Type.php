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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Entity type model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{
    /**
     * Collection of attributes
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    protected $_sets;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }

    /**
     * Load type by code
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Type
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
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        $objectsModel = $this->getAttributeModel();
        if ($objectsModel) {
            $collection->setModel($objectsModel);
        }

        return $collection;
    }

    /**
     * Retrieve entity tpe sets collection
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    public function getAttributeSetCollection()
    {
        if (empty($this->_sets)) {
            $this->_sets = Mage::getModel('eav/entity_attribute_set')->getResourceCollection()
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

        $entityStoreConfig = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($this->getId(), $storeId);

        if (!$entityStoreConfig->getId()) {
            $entityStoreConfig
                ->setEntityTypeId($this->getId())
                ->setStoreId($storeId)
                ->setIncrementPrefix($storeId)
                ->save();
        }

        $incrementInstance = Mage::getModel($this->getIncrementModel())
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
     * Retreive value table prefix
     *
     * @return string
     */
    public function getValueTablePrefix()
    {
        if (empty($this->_data['value_table_prefix'])) {
            $this->_data['value_table_prefix'] = $this->_getResource()->getTable($this->getEntityTable());
        }
        return $this->_data['value_table_prefix'];
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
            return Mage_Eav_Model_Entity::DEFAULT_ATTRIBUTE_MODEL;
        }

        return $this->_data['attribute_model'];
    }

    /**
     * Retreive resource entity object
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getEntity()
    {
        return Mage::getResourceSingleton($this->_data['entity_model']);
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
        return 'eav/entity_attribute_collection';
    }
}
