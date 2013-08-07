<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Eav_Model_Entity_Attribute_Backend_Store extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before save
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Store
     */
    protected function _beforeSave($object)
    {
        if (!$object->getData($this->getAttribute()->getAttributeCode())) {
            $object->setData($this->getAttribute()->getAttributeCode(), Mage::app()->getStore()->getId());
        }

        return $this;
    }
}
