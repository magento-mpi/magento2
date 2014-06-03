<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;


class QuoteDetails extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_BILLING_ADDRESS = 'billing_address';

    const KEY_SHIPPING_ADDRESS = 'shipping_address';

    const KEY_TAX_CLASS_ID = 'tax_class_id';

    const KEY_CUSTOMER = 'customer';

    const KEY_CUSTOMER_GROUP = 'customer_group';

    const KEY_ITEMS = 'items';
    /**#@-*/

    /**
     * Get customer billing address
     *
     * @return \Magento\Customer\Service\V1\Data\Address|null
     */
    public function getBillingAddress()
    {
        return $this->_get(self::KEY_BILLING_ADDRESS);
    }

    /**
     * Get customer shipping address
     *
     * @return \Magento\Customer\Service\V1\Data\Address|null
     */
    public function getShippingAddress()
    {
        return $this->_get(self::KEY_SHIPPING_ADDRESS);
    }

    /**
     * Get customer tax class id
     *
     * @return int|null
     */
    public function getCustomerTaxClassId()
    {
        return $this->_get(self::KEY_TAX_CLASS_ID);
    }

    /**
     * Get customer
     *
     * @return \Magento\Customer\Service\V1\Data\Customer|null
     */
    public function getCustomer()
    {
        return $this->_get(self::KEY_CUSTOMER);
    }

    /**
     * Get customer group
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup|null
     */
    public function getCustomerGroup()
    {
        return $this->_get(self::KEY_CUSTOMER_GROUP);
    }

    /**
     * Get quote items
     *
     * @return \Magento\Tax\Service\V1\Data\QuoteDetails\Item []|null
     */
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS);
    }
}
