<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;

/**
 * Test Creation for CreateInvoiceEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable payment method "Check/Money Order"
 * 2. Enable shipping method one of "Flat Rate/Free Shipping"
 * 3. Create order
 *
 * Steps:
 * 1. Go to Sales > Orders
 * 2. Select created order in the grid and open it
 * 3. Click 'Invoice' button
 * 4. Fill data according to dataSet
 * 5. Click 'Submit Invoice' button
 * 6. Perform assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28209
 */
class CreateInvoiceEntityTest extends Injectable
{
    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set up configuration
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $configPayment = $fixtureFactory->createByCode('configData', ['dataSet' => 'checkmo']);
        $configPayment->persist();

        $configShipping = $fixtureFactory->createByCode('configData', ['dataSet' => 'flatrate']);
        $configShipping->persist();
    }

    /**
     * Injection data
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __inject(
        CustomerAccountLogout $customerAccountLogout,
        ObjectManager $objectManager
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->objectManager = $objectManager;
    }

    /**
     * Create invoice
     *
     * @param OrderInjectable $order
     * @param array $invoice
     * @return array
     */
    public function test(OrderInjectable $order, array $invoice)
    {
        // Preconditions
        $order->persist();

        // Steps
        $createInvoice = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateInvoiceStep',
            ['order' => $order, 'data' => $invoice]
        );
        $data = $createInvoice->run();

        return [
            'ids' => [
                'invoiceIds' => $data['invoiceIds'],
                'shipmentIds' => isset($data['shipmentIds']) ? $data['shipmentIds'] : null,
            ],
            'successMessage' => $data['successMessage'],
        ];
    }

    /**
     * Log out
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
