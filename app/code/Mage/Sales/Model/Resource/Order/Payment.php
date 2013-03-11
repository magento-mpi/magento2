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
 * Flat sales order payment resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Payment extends Mage_Sales_Model_Resource_Order_Abstract
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
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_converter = Mage::getSingleton('Mage_Sales_Model_Payment_Method_Converter');
        $this->_init('sales_flat_order_payment', 'entity_id');
    }
}
