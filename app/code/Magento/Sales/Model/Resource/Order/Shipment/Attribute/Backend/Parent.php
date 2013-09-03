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
class Magento_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Parent
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Performed after data is saved
     *
     * @param \Magento\Object $object
     * @return Magento_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Parent
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
