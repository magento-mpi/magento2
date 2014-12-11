<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Fixture for registered customer checkout
 */
class RegisteredPaypalExpress extends GuestPaypalExpress
{
    /**
     * Get configuration fixtures
     *
     * @return array
     */
    protected function _getConfigFixtures()
    {
        $list = parent::_getConfigFixtures();
        $list[] = 'address_template';
        return $list;
    }

    /**
     * Get billing address for checkout
     *
     * @return \Magento\Customer\Test\Fixture\AddressBook
     */
    protected function _initBillingAddress()
    {
        $billing = Factory::getFixtureFactory()->getMagentoCustomerAddressBook();
        $billing->setAddress(parent::_initBillingAddress());
        return $billing;
    }
}
