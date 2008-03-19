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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Entity/Attribute/Model - attribute backend abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 */
abstract class Mage_Eav_Model_Entity_Attribute_Backend_Abstract implements Mage_Eav_Model_Entity_Attribute_Backend_Interface
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
     * Get table name for the values of the attribute
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->_table)) {
            $this->_table = $this->getAttribute()->getBackendTable();
        }

        return $this->_table;
    }

    public function getDefaultValue()
    {
        if (is_null($this->_defaultValue)) {
            if ($this->getAttribute()->getDefaultValue()) {
                $this->_defaultValue = $this->getAttribute()->getDefaultValue();
            } else {
                $this->_defaultValue = "";
            }
        }
        return $this->_defaultValue;
    }

    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($this->getAttribute()->getIsRequired() && !$object->getData($attrCode)) {
            return false;
        }
        if ($this->getAttribute()->getIsUnique() && !$this->getAttribute()->getIsRequired() && $this->getAttribute()->isValueEmpty($object->getData($this->getAttribute()->getAttributeCode()))) {
            return true;
        }
        if ($this->getAttribute()->getIsUnique()) {
            if (!$object->getResource()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                Mage::throwException(Mage::helper('eav')->__('Value of attribute "%s" must be unique', $label));
            }
        }
        return true;
    }

    public function afterLoad($object)
    {

    }

    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if (!$object->hasData($attrCode) && $this->getDefaultValue()) {
            $object->setData($attrCode, $this->getDefaultValue());
        }
    }

    public function afterSave($object)
    {

    }

    public function beforeDelete($object)
    {

    }

    public function afterDelete($object)
    {

    }
}
