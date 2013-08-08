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
 * Entity attribute backend interface
 *
 * Backend is responsible for saving the values of the attribute
 * and performing pre and post actions
 *
 */
interface Mage_Eav_Model_Entity_Attribute_Backend_Interface
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
     * @param Varien_Object $entity
     */
    public function getEntityValueId($entity);

    /**
     * Set entity value id
     *
     * @param Varien_Object $entity
     * @param int $valueId
     */
    public function setEntityValueId($entity, $valueId);

    /**
     * Format attribute value in accordance with the backend type
     *
     * @param mixed $value
     * @return mixed
     */
    public function prepareValueForSave($value);
}
