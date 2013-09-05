<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Eav_Model_Entity_Attribute_Backend_Store extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before save
     *
     * @param \Magento\Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Store
     */
    protected function _beforeSave($object)
    {
        if (!$object->getData($this->getAttribute()->getAttributeCode())) {
            $object->setData($this->getAttribute()->getAttributeCode(), Mage::app()->getStore()->getId());
        }

        return $this;
    }
}
