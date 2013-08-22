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
 *Quote address attribute backend parent resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Save items collection and shipping rates collection
     *
     * @param Magento_Object $object
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent
     */
    public function afterSave($object)
    {
        parent::afterSave($object);
        
        $object->getItemsCollection()->save();
        $object->getShippingRatesCollection()->save();
        
        return $this;
    }
}
