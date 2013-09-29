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
 * Quote address shipping rate resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Address;

class Rate extends \Magento\Sales\Model\Resource\AbstractResource
{
    /**
     * Main table and field initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_quote_shipping_rate', 'rate_id');
    }
}
