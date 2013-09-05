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
 * "Serialized" attribute backend
 */
class Magento_Eav_Model_Entity_Attribute_Backend_Serialized extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Serialize before saving
     *
     * @param \Magento\Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Serialized
     */
    public function beforeSave($object)
    {
        // parent::beforeSave() is not called intentionally
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode)) {
            $object->setData($attrCode, serialize($object->getData($attrCode)));
        }

        return $this;
    }

    /**
     * Unserialize after saving
     *
     * @param \Magento\Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Serialized
     */
    public function afterSave($object)
    {
        parent::afterSave($object);
        $this->_unserialize($object);
        return $this;
    }

    /**
     * Unserialize after loading
     *
     * @param \Magento\Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Serialized
     */
    public function afterLoad($object)
    {
        parent::afterLoad($object);
        $this->_unserialize($object);
        return $this;
    }

    /**
     * Try to unserialize the attribute value
     *
     * @param \Magento\Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Serialized
     */
    protected function _unserialize(\Magento\Object $object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->getData($attrCode)) {
            try {
                $unserialized = unserialize($object->getData($attrCode));
                $object->setData($attrCode, $unserialized);
            } catch (Exception $e) {
                $object->unsetData($attrCode);
            }
        }

        return $this;
    }
}
