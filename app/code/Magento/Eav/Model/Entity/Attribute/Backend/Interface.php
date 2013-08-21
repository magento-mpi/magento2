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
 * Entity attribute backend interface
 *
 * Backend is responsible for saving the values of the attribute
 * and performing pre and post actions
 *
 */
interface Magento_Eav_Model_Entity_Attribute_Backend_Interface
{
    public function getTable();
    public function isStatic();
    public function getType();
    public function getEntityIdField();
    public function setValueId($valueId);
    public function getValueId();
    public function afterLoad($object);
    public function beforeSave($object);
    public function afterSave($object);
    public function beforeDelete($object);
    public function afterDelete($object);

    /**
     * Get entity value id
     *
     * @param Magento_Object $entity
     */
    public function getEntityValueId($entity);

    /**
     * Set entity value id
     *
     * @param Magento_Object $entity
     * @param int $valueId
     */
    public function setEntityValueId($entity, $valueId);

    /**
     * Whether an attribute is represented by a scalar value that can be stored in a generic way
     *
     * @return bool
     */
    public function isScalar();
}
