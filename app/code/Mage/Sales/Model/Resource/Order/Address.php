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
 * Flat sales order address resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Address extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_resource';

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_order_address', 'entity_id');
    }

    /**
     * Return configuration for all attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributes = array(
            'city'       => Mage::helper('Mage_Sales_Helper_Data')->__('City'),
            'company'    => Mage::helper('Mage_Sales_Helper_Data')->__('Company'),
            'country_id' => Mage::helper('Mage_Sales_Helper_Data')->__('Country'),
            'email'      => Mage::helper('Mage_Sales_Helper_Data')->__('Email'),
            'firstname'  => Mage::helper('Mage_Sales_Helper_Data')->__('First Name'),
            'lastname'   => Mage::helper('Mage_Sales_Helper_Data')->__('Last Name'),
            'region_id'  => Mage::helper('Mage_Sales_Helper_Data')->__('State/Province'),
            'street'     => Mage::helper('Mage_Sales_Helper_Data')->__('Street Address'),
            'telephone'  => Mage::helper('Mage_Sales_Helper_Data')->__('Telephone'),
            'postcode'   => Mage::helper('Mage_Sales_Helper_Data')->__('Zip/Postal Code')
        );
        asort($attributes);
        return $attributes;
    }

    /**
     * Update related grid table after object save
     *
     * @param Magento_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $resource = parent::_afterSave($object);
        if ($object->hasDataChanges() && $object->getOrder()) {
            $gridList = array(
                'Mage_Sales_Model_Resource_Order' => 'entity_id',
                'Mage_Sales_Model_Resource_Order_Invoice' => 'order_id',
                'Mage_Sales_Model_Resource_Order_Shipment' => 'order_id',
                'Mage_Sales_Model_Resource_Order_Creditmemo' => 'order_id'
            );

            // update grid table after grid update
            foreach ($gridList as $gridResource => $field) {
                Mage::getResourceModel($gridResource)->updateOnRelatedRecordChanged(
                    $field,
                    $object->getParentId()
                );
            }
        }

        return $resource;
    }
}
