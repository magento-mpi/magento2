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
 * Customer Order model
 *
 * @method Magento_CustomerCustomAttributes_Model_Resource_Sales_Order _getResource()
 * @method Magento_CustomerCustomAttributes_Model_Resource_Sales_Order getResource()
 * @method Magento_CustomerCustomAttributes_Model_Sales_Order setEntityId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Model_Sales_Order extends Magento_CustomerCustomAttributes_Model_Sales_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CustomerCustomAttributes_Model_Resource_Sales_Order');
    }
}
