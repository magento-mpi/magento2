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
 * Order Tax Model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Tax extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_order_tax', 'tax_id');
    }
}
