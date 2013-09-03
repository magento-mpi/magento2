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
 * Invoice backend model for parent attribute
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Attribute_Backend_Parent extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation after save
     *
     * @param \Magento\Object $object
     * @return Magento_Sales_Model_Resource_Order_Attribute_Backend_Parent
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        foreach ($object->getAddressesCollection() as $item) {
            $item->save();
        }
        foreach ($object->getItemsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getPaymentsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getStatusHistoryCollection() as $item) {
            $item->save();
        }
        foreach ($object->getRelatedObjects() as $object) {
            $object->save();
        }
        return $this;
    }
}
