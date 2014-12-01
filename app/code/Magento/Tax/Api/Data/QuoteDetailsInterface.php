<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddress;

interface QuoteDetailsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_BILLING_ADDRESS = 'billing_address';

    const KEY_SHIPPING_ADDRESS = 'shipping_address';

    const KEY_CUSTOMER_TAX_CLASS_KEY = 'customer_tax_class_key';

    const KEY_ITEMS = 'items';

    const CUSTOMER_TAX_CLASS_ID = 'customer_tax_class_id';

    const KEY_CUSTOMER_ID = 'customer_id';
    /**#@-*/

    /**
     * Get customer billing address
     *
     * @return CustomerAddress|null
     */
    public function getBillingAddress();

    /**
     * Get customer shipping address
     *
     * @return CustomerAddress|null
     */
    public function getShippingAddress();

    /**
     * Get customer tax class key
     *
     * @return \Magento\Tax\Api\Data\TaxClassKeyInterface|null
     */
    public function getCustomerTaxClassKey();

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get customer data
     *
     * @return \Magento\Tax\Api\Data\QuoteDetailsItemInterface[]|null
     */
    public function getItems();

    /**
     * Get customer tax class id
     *
     * @return int
     */
    public function getCustomerTaxClassId();
}
