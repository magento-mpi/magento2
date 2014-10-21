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
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __prepare(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        $setupConfigurationStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkmo, flatrate']
        );
        $setupConfigurationStep->run();
    }

    /**
     * Injection data
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(CustomerAccountLogout $customerAccountLogout)
    {
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Create invoice
     *
     * @param OrderInjectable $order
     * @param array $data
     * @return array
     */
    public function test(OrderInjectable $order, array $data)
    {
        // Preconditions
        $order->persist();

        // Steps
        $createInvoice = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateInvoiceStep',
            ['order' => $order, 'data' => $data]
        );
        $result = $createInvoice->run();

        return [
            'ids' => [
                'invoiceIds' => $result['invoiceIds'],
                'shipmentIds' => isset($result['shipmentIds']) ? $result['shipmentIds'] : null,
            ]
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
