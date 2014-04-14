<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Backend;

/**
 * Entity attribute backend interface
 *
 * Backend is responsible for saving the values of the attribute
 * and performing pre and post actions
 *
 */
interface BackendInterface
{
    /**
     * @return string
     */
    public function getTable();

    /**
     * @return bool
     */
    public function isStatic();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getEntityIdField();

    /**
     * @param int $valueId
     * @return $this
     */
    public function setValueId($valueId);

    /**
     * @return int
     */
    public function getValueId();

    /**
     * @param \Magento\Object $object
     * @return $this
     */
    public function afterLoad($object);

    /**
     * @param \Magento\Object $object
     * @return $this
     */
    public function beforeSave($object);

    /**
     * @param \Magento\Object $object
     * @return $this
     */
    public function afterSave($object);

    /**
     * @param \Magento\Object $object
     * @return $this
     */
    public function beforeDelete($object);

    /**
     * @param \Magento\Object $object
     * @return $this
     */
    public function afterDelete($object);

    /**
     * Get entity value id
     *
     * @param \Magento\Object $entity
     * @return int
     */
    public function getEntityValueId($entity);

    /**
     * Set entity value id
     *
     * @param \Magento\Object $entity
     * @param int $valueId
     * @return $this
     */
    public function setEntityValueId($entity, $valueId);

    /**
     * Whether an attribute is represented by a scalar value that can be stored in a generic way
     *
     * @return bool
     */
    public function isScalar();
}
