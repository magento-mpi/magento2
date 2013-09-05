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
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute_Backend_Time_Updated extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set modified date
     *
     * @param \Magento\Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Time_Updated
     */
    public function beforeSave($object)
    {
        $object->setData($this->getAttribute()->getAttributeCode(), \Magento\Date::now());
        return $this;
    }
}
