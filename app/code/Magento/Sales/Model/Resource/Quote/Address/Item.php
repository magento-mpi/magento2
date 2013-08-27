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
 * Quote address item resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Item extends Magento_Sales_Model_Resource_Abstract
{
    /**
     * Main table and field initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_quote_address_item', 'address_item_id');
    }
}
