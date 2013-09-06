<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Order Address model
 *
 * @method Magento_CustomerCustomAttributes_Model_Resource_Sales_Order_Address _getResource()
 * @method Magento_CustomerCustomAttributes_Model_Resource_Sales_Order_Address getResource()
 * @method Magento_CustomerCustomAttributes_Model_Sales_Order_Address setEntityId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Model_Sales_Order_Address extends Magento_CustomerCustomAttributes_Model_Sales_Address_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CustomerCustomAttributes_Model_Resource_Sales_Order_Address');
    }
}
