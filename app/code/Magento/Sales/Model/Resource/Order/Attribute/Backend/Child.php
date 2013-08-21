<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invoice backend model for child attribute
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Attribute_Backend_Child extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation before save
     *
     * @param Magento_Object $object
     * @return Magento_Sales_Model_Resource_Order_Attribute_Backend_Child
     */
    public function beforeSave($object)
    {
        if ($object->getOrder()) {
            $object->setParentId($object->getOrder()->getId());
        }
        parent::beforeSave($object);
        return $this;
    }
}
