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
 * Invoice backend model for parent attribute
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Parent
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Performed after data is saved
     *
     * @param Magento_Object $object
     * @return Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Parent
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        /**
         * Save Shipment items
         */
        foreach ($object->getAllItems() as $item) {
            $item->save();
        }

        /**
         * Save Shipment tracks
         */
        foreach ($object->getAllTracks() as $track) {
            $track->save();
        }

        foreach ($object->getCommentsCollection() as $comment) {
            $comment->save();
        }
        return $this;
    }
}
