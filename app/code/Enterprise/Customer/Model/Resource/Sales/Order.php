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
 * Customer Order resource
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Model_Resource_Sales_Order extends Enterprise_Customer_Model_Resource_Sales_Abstract
{
    /**
     * Main entity resource model name
     *
     * @var string
     */
    protected $_parentResourceModelName = 'Magento_Sales_Model_Resource_Order';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('enterprise_customer_sales_flat_order', 'entity_id');
    }
}
