<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Submit Order step.
 */
class SubmitOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Sales order view.
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * Factory for fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param OrderView $orderView
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @param AddressInjectable $billingAddress
     * @param \Mtf\Fixture\FixtureInterface[] $products
     */
    public function __construct(
        OrderCreateIndex $orderCreateIndex,
        OrderView $orderView,
        FixtureFactory $fixtureFactory,
        CustomerInjectable $customer,
        AddressInjectable $billingAddress,
        array $products
    ) {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->orderView = $orderView;
        $this->fixtureFactory = $fixtureFactory;
        $this->customer = $customer;
        $this->billingAddress = $billingAddress;
        $this->products = $products;
    }

    /**
     * Fill Sales Data.
     *
     * @return array
     */
    public function run()
    {
        $this->orderCreateIndex->getCreateBlock()->submitOrder();
        $this->orderView->getMessagesBlock()->waitSuccessMessage();
        $order = $this->fixtureFactory->createByCode(
            'orderInjectable',
            [
                'data' => [
                    'id' => trim($this->orderView->getTitleBlock()->getTitle(), '#'),
                    'customer_id' => ['customer' => $this->customer],
                    'entity_id' => ['products' => $this->products],
                    'billing_address_id' => ['billingAddress' => $this->billingAddress],
                ]
            ]
        );

        return ['orderId' => trim($this->orderView->getTitleBlock()->getTitle(), '#'), 'order' => $order];
    }
}
