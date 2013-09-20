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
 * Flat sales order address resource
 */
class Magento_Sales_Model_Resource_Order_Address extends Magento_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_resource';

    /**
     * @var Magento_Sales_Model_Resource_Factory
     */
    protected $_salesResourceFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Eav_Model_Entity_TypeFactory $eavEntityTypeFactory
     * @param Magento_Sales_Model_Resource_Factory $salesResourceFactory
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Eav_Model_Entity_TypeFactory $eavEntityTypeFactory,
        Magento_Sales_Model_Resource_Factory $salesResourceFactory
    ) {
        parent::__construct($eventManager, $resource, $eavEntityTypeFactory);
        $this->_salesResourceFactory = $salesResourceFactory;
    }

    /**
     * Resource initialization
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
            'city'       => __('City'),
            'company'    => __('Company'),
            'country_id' => __('Country'),
            'email'      => __('Email'),
            'firstname'  => __('First Name'),
            'lastname'   => __('Last Name'),
            'region_id'  => __('State/Province'),
            'street'     => __('Street Address'),
            'telephone'  => __('Telephone'),
            'postcode'   => __('Zip/Postal Code')
        );
        asort($attributes);
        return $attributes;
    }

    /**
     * Update related grid table after object save
     *
     * @param Magento_Core_Model_Abstract|Magento_Object $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $resource = parent::_afterSave($object);
        if ($object->hasDataChanges() && $object->getOrder()) {
            $gridList = array(
                'Magento_Sales_Model_Resource_Order' => 'entity_id',
                'Magento_Sales_Model_Resource_Order_Invoice' => 'order_id',
                'Magento_Sales_Model_Resource_Order_Shipment' => 'order_id',
                'Magento_Sales_Model_Resource_Order_Creditmemo' => 'order_id'
            );

            // update grid table after grid update
            foreach ($gridList as $gridResource => $field) {
                $this->_salesResourceFactory->create($gridResource)->updateOnRelatedRecordChanged(
                    $field,
                    $object->getParentId()
                );
            }
        }

        return $resource;
    }
}
