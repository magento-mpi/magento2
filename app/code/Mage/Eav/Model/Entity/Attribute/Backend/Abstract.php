<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Entity/Attribute/Model - attribute backend abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Entity_Attribute_Backend_Abstract
    implements Mage_Eav_Model_Entity_Attribute_Backend_Interface
{
    /**
     * Reference to the attribute instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attribute;

    /**
     * PK value_id for loaded entity (for faster updates)
     *
     * @var integer
     */
    protected $_valueId;

    /**
     * PK value_ids for each loaded entity
     *
     * @var array
     */
    protected $_valueIds = array();

    /**
     * Table name for this attribute
     *
     * @var string
     */
    protected $_table;

    /**
     * Name of the entity_id field for the value table of this attribute
     *
     * @var string
     */
    protected $_entityIdField;

    /**
     * Default value for the attribute
     *
     * @var mixed
     */
    protected $_defaultValue = null;

    /**
     * Set attribute instance
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Get backend type of the attribute
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute()->getBackendType();
    }

    /**
     * Check whether the attribute is a real field in the entity table
     *
     * @return boolean
     */
    public function isStatic()
    {
        return $this->getAttribute()->isStatic();
    }

    /**
     * Get table name for the values of the attribute
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->_table)) {
            if ($this->isStatic()) {
                $this->_table = $this->getAttribute()->getEntityType()->getValueTablePrefix();
            } elseif ($this->getAttribute()->getBackendTable()) {
                $this->_table = $this->getAttribute()->getBackendTable();
            } else {
                $entity = $this->getAttribute()->getEntity();
                $tableName = sprintf('%s_%s', $entity->getValueTablePrefix(), $this->getType());
                $this->_table = $tableName;
            }
        }

        return $this->_table;
    }

    /**
     * Get entity_id field in the attribute values tables
     *
     * @return string
     */
    public function getEntityIdField()
    {
        if (empty($this->_entityIdField)) {
            if ($this->getAttribute()->getEntityIdField()) {
                $this->_entityIdField = $this->getAttribute()->getEntityIdField();
            } else {
                $this->_entityIdField = $this->getAttribute()->getEntityType()->getValueEntityIdField();
            }
        }

        return $this->_entityIdField;
    }

    /**
     * Set value id
     *
     * @param int $valueId
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function setValueId($valueId)
    {
        $this->_valueId = $valueId;
        return $this;
    }

    /**
     * Set entity value id
     *
     * @param Magento_Object $entity
     * @param int $valueId
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function setEntityValueId($entity, $valueId)
    {
        if (!$entity || !$entity->getId()) {
            return $this->setValueId($valueId);
        }

        $this->_valueIds[$entity->getId()] = $valueId;
        return $this;
    }

    /**
     * Retrieve value id
     *
     * @return int
     */
    public function getValueId()
    {
        return $this->_valueId;
    }

    /**
     * Get entity value id
     *
     * @param Magento_Object $entity
     * @return int
     */
    public function getEntityValueId($entity)
    {
        if (!$entity || !$entity->getId() || !array_key_exists($entity->getId(), $this->_valueIds)) {
            return $this->getValueId();
        }

        return $this->_valueIds[$entity->getId()];
    }

    /**
     * Retrieve default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->_defaultValue === null) {
            if ($this->getAttribute()->getDefaultValue()) {
                $this->_defaultValue = $this->getAttribute()->getDefaultValue();
            } else {
                $this->_defaultValue = "";
            }
        }

        return $this->_defaultValue;
    }

    /**
     * Validate object
     *
     * @param Magento_Object $object
     * @throws Mage_Eav_Exception
     * @return boolean
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($this->getAttribute()->getIsRequired() && $this->getAttribute()->isValueEmpty($value)) {
            return false;
        }

        if ($this->getAttribute()->getIsUnique()
            && !$this->getAttribute()->getIsRequired()
            && ($value == '' || $this->getAttribute()->isValueEmpty($value)))
        {
            return true;
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                throw Mage::exception('Mage_Eav',
                    Mage::helper('Mage_Eav_Helper_Data')->__('The value of attribute "%s" must be unique', $label)
                );
            }
        }

        return true;
    }

    /**
     * After load method
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        return $this;
    }

    /**
     * Before save method
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if (!$object->hasData($attrCode) && $this->getDefaultValue()) {
            $object->setData($attrCode, $this->getDefaultValue());
        }

        return $this;
    }

    /**
     * After save method
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterSave($object)
    {
        return $this;
    }

    /**
     * Before delete method
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeDelete($object)
    {
        return $this;
    }

    /**
     * After delete method
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterDelete($object)
    {
        return $this;
    }

    /**
     * Retrieve data for update attribute
     *
     * @param Magento_Object $object
     * @return array
     */
    public function getAffectedFields($object)
    {
        $data = array();
        $data[$this->getTable()][] = array(
            'attribute_id' => $this->getAttribute()->getAttributeId(),
            'value_id' => $this->getEntityValueId($object),
        );
        return $data;
    }
}
