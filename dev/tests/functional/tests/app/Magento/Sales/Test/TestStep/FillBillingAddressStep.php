<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Class FillBillingAddressStep
 * Fill Sales Data
 */
class FillBillingAddressStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Customer
     *
     * @var AddressInjectable
     */
    protected $billingAddress;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param AddressInjectable $billingAddress
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, AddressInjectable $billingAddress)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->billingAddress = $billingAddress;
    }

    /**
     * Fill Sales Data
     *
     * @return AddressInjectable
     */
    public function run()
    {
        $this->orderCreateIndex->getCreateBlock()->fillAddresses($this->billingAddress);

        return ['billingAddress' => $this->billingAddress];
    }
}
