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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sale api resource abstract
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Api_Resource extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Default ignored attribute codes per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array(
        'global'    =>  array('entity_id', 'attribute_set_id', 'entity_type_id')
    );

    /**
     * Default ignored attribute types per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array(
        'global'    =>  array()
    );

    /**
     * Attributes map array per entity type
     *
     * @var google
     */
    protected $_attributesMap = array(
        'global'    => array()
    );

    /**
     * Update attributes for entity
     *
     * @param array $data
     * @param Mage_Core_Model_Abstract $object
     * @param array $attributes
     * @return Mage_Sales_Model_Api_Resource
     */
    protected function _updateAttributes($data, $object, array $attributes = null)
    {
        $entityType = $object->getResource()->getType();
        $attributesList = $object->getResource()->loadAllAttributes()->getAttributesByCode();

        foreach ($attributesList as $attributeCode=>$attribute) {
            if ($this->_isAllowedAttribute($attribute, $entityType, $attributes)
                && isset($data[$attributeCode])) {
                $object->setData($attributeCode, $data[$attributeCode]);
            }
        }

        return $this;
    }

    /**
     * Retrieve entity attributes values
     *
     * @param Mage_Core_Model_Abstract $object
     * @param array $attributes
     * @return Mage_Sales_Model_Api_Resource
     */
    protected function _getAttributes($object, array $attributes = null)
    {
        $entityType = $object->getResource()->getType();
        $attributesList = $object->getResource()->loadAllAttributes()->getAttributesByCode();
        $result = array();

        foreach ($attributesList as $attributeCode=>$attribute) {
            if ($this->_isAllowedAttribute($attribute, $entityType, $attributes)) {
                $result[$attributeCode] = $object->getData($attributeCode);
            }
        }

        foreach ($this->_attributesMap['global'] as $alias=>$attributeCode) {
            $result[$alias] = $object->getData($attributeCode);
        }

        if (isset($this->_attributesMap[$entityType])) {
            foreach ($this->_attributesMap[$entityType] as $alias=>$attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        return $result;
    }

    /**
     * Check is attribute allowed to usage
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $entityType
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $entityType, array $attributes = null)
    {
        if (!empty($attributes)
            && !( in_array($attribute->getAttributeCode(), $attributes)
                  || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        if (in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes['global'])
               || in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes['global'])) {
            return false;
        }

        if (isset($this->_ignoredAttributeTypes[$entityType])
            && in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes[$entityType])) {
            return false;
        }

        if (isset($this->_ignoredAttributeCodes[$entityType])
            && in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes[$entityType])) {
            return false;
        }

        return true;
    }
} // Class Mage_Sales_Model_Api_Resource End