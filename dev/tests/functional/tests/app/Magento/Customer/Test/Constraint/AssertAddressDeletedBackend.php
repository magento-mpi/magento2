<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertAddressDeletedBackend
 * Asserts that deleted customers address does not displays on backend during order creation
 */
class AssertAddressDeletedBackend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that deleted customers address does not displays on backend during order creation
     *
     * @param OrderIndex $orderIndex
     * @param OrderCreateIndex $orderCreateIndex
     * @param AddressInjectable $deletedAddress
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        OrderIndex $orderIndex,
        OrderCreateIndex $orderCreateIndex,
        AddressInjectable $deletedAddress,
        CustomerInjectable $customer
    ) {
        $filter = ['email' => $customer->getEmail()];
        $orderIndex->open()->getGridPageActions()->addNew();
        $orderCreateIndex->getCustomerBlock()->searchAndOpen($filter);
        $orderCreateIndex->getStoreBlock()->selectStoreView();
        $actualAddresses = $orderCreateIndex->getCreateBlock()->getBillingAddressBlock()->getExistingAddresses();
        $addressToSearch = $orderCreateIndex->getCreateBlock()->prepareAddress($deletedAddress);
        \PHPUnit_Framework_Assert::assertFalse(
            in_array($addressToSearch, $actualAddresses),
            'Expected text is present in Additional Address dropdown'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Expected text is absent in Additional Address dropdown';
    }
}
