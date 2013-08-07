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
 * "Serialized" attribute backend
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Serialized extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Serialize before saving
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Serialized
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
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Serialized
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
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Serialized
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
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Serialized
     */
    protected function _unserialize(Magento_Object $object)
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
