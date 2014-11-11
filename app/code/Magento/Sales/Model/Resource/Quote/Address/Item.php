<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote\Address;

/**
 * Quote address item resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Sales\Model\Resource\AbstractResource
{
    /**
     * Main table and field initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_quote_address_item', 'address_item_id');
    }
}
