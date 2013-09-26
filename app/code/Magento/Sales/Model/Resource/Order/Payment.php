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
 * Flat sales order payment resource
 */
class Magento_Sales_Model_Resource_Order_Payment extends Magento_Sales_Model_Resource_Order_Abstract
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix          = 'sales_order_payment_resource';

    /**
     * @var Magento_Sales_Model_Payment_Method_Converter
     */
    protected $_paymentConverter;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Eav_Model_Entity_TypeFactory $eavEntityTypeFactory
     * @param Magento_Sales_Model_Payment_Method_Converter $paymentConverter
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Eav_Model_Entity_TypeFactory $eavEntityTypeFactory,
        Magento_Sales_Model_Payment_Method_Converter $paymentConverter
    ) {
        $this->_paymentConverter = $paymentConverter;
        parent::__construct($eventManager, $resource, $eavEntityTypeFactory);
    }

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_converter = $this->_paymentConverter;
        $this->_init('sales_flat_order_payment', 'entity_id');
    }
}
