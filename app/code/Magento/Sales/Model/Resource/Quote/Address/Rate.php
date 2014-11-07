<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote\Address;

/**
 * Quote address shipping rate resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rate extends \Magento\Sales\Model\Resource\AbstractResource
{
    /**
     * Main table and field initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_quote_shipping_rate', 'rate_id');
    }
}
