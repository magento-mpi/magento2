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
 * Quote payment resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Payment extends Mage_Sales_Model_Resource_Abstract
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
     * Main table and field initialization
     *
     */
    protected function _construct()
    {
        $this->_converter = Mage::getSingleton('Mage_Sales_Model_Payment_Method_Converter');
        $this->_init('sales_flat_quote_payment', 'payment_id');
    }
}
