<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Fixture\FixtureFactory;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Test Creation for CreateCreditMemo for offline payment methods
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable payment method "Check/Money Order"
 * 2. Enable shipping method one of "Flat Rate/Free Shipping"
 * 3. Create order
 * 4. Create Invoice.
 *
 * Steps:
 * 1. Go to Sales > Orders > find out placed order and open
 * 2. Click 'Credit Memo' button
 * 3. Fill data from dataSet
 * 4. On order's page click 'Refund offline' button
 * 5. Perform all assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-29116
 */
class CreateCreditMemoEntityTest extends Injectable
{
    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Skip fields for create product fixture
     *
     * @var array
     */
    protected $skipFields = [
        'attribute_set_id',
        'website_ids',
        'checkout_data',
        'type_id',
        'price'
    ];

    /**
     * Set up configuration
     *
     * @param ObjectManager $objectManager
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(ObjectManager $objectManager, FixtureFactory $fixtureFactory)
    {
        $this->objectManager = $objectManager;
        $this->fixtureFactory = $fixtureFactory;

        $setupConfigurationStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkmo, flatrate']
        );
        $setupConfigurationStep->run();
    }

    /**
     * Create credit memo
     *
     * @param OrderInjectable $order
     * @param array $data
     * @return array
     */
    public function test(OrderInjectable $order, array $data)
    {
        // Preconditions
        $order->persist();
        $this->objectManager->create('Magento\Sales\Test\TestStep\CreateInvoiceStep', ['order' => $order])->run();

        // Steps
        $createCreditMemoStep = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateCreditMemoStep',
            ['order' => $order, 'data' => $data]
        );
        $result = $createCreditMemoStep->run();

        return [
            'ids' => ['creditMemoIds' => $result['creditMemoIds']],
            'product' => $this->getProduct($order, $data),
            'customer' => $order->getDataFieldConfig('customer_id')['source']->getCustomer()
        ];
    }

    /**
     * Get product's fixture
     *
     * @param OrderInjectable $order
     * @param array $data
     * @param int $index [optional]
     * @return FixtureInterface
     */
    protected function getProduct(OrderInjectable $order, array $data, $index = 0)
    {
        if ($data['items_data'][$index]['back_to_stock'] != 'Yes') {
            return $order->getEntityId()['products'][$index];
        }
        $product = $order->getEntityId()['products'][$index];
        $productData = $product->getData();
        $checkoutDataQty = $productData['checkout_data']['options']['qty'];
        $productData['quantity_and_stock_status']['qty'] -= ($checkoutDataQty - $data['items_data'][$index]['qty']);
        $productData = array_diff_key($productData, array_flip($this->skipFields));

        return $this->fixtureFactory->create(get_class($product), ['data' => $productData]);
    }
}
