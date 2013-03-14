<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Order Address model
 *
 * @method Enterprise_Customer_Model_Resource_Sales_Order_Address _getResource()
 * @method Enterprise_Customer_Model_Resource_Sales_Order_Address getResource()
 * @method Enterprise_Customer_Model_Sales_Order_Address setEntityId(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Model_Sales_Order_Address extends Enterprise_Customer_Model_Sales_Address_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Customer_Model_Resource_Sales_Order_Address');
    }
}
