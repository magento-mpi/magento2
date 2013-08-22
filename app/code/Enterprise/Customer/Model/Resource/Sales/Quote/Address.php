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
 * Customer Quote Address resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Model_Resource_Sales_Quote_Address
    extends Enterprise_Customer_Model_Resource_Sales_Address_Abstract
{
    /**
     * Main entity resource model name
     *
     * @var string
     */
    protected $_parentResourceModelName = 'Magento_Sales_Model_Resource_Quote_Address';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('enterprise_customer_sales_flat_quote_address', 'entity_id');
    }
}
