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
 * Customer Order resource
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Model_Resource_Sales_Order extends Magento_CustomerCustomAttributes_Model_Resource_Sales_Abstract
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
        $this->_init('magento_customercustomattributes_sales_flat_order', 'entity_id');
    }
}
