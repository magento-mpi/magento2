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
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set modified date
     *
     * @param Magento_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated
     */
    public function beforeSave($object)
    {
        $object->setData($this->getAttribute()->getAttributeCode(), Magento_Date::now());
        return $this;
    }
}
