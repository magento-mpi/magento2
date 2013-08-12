<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invoice backend model for child attribute
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Child
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Method is invoked before save
     *
     * @param Magento_Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        if ($object->getInvoice()) {
            $object->setParentId($object->getInvoice()->getId());
        }
        return parent::beforeSave($object);
    }
}
