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
 * Customer Order Address resource model
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Model_Resource_Sales_Order_Address
    extends Magento_CustomerCustomAttributes_Model_Resource_Sales_Address_Abstract
{
    /**
     * Main entity resource model name
     *
     * @var string
     */
    protected $_parentResourceModelName = 'Magento_Sales_Model_Resource_Order_Address';

    /**
     * Initializes resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_order_address', 'entity_id');
    }
}
