<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Magento\Rma\Test\Page\Adminhtml\RmaIndex;
use Magento\Rma\Test\Page\Adminhtml\RmaNew;
use Magento\Rma\Test\Page\Adminhtml\RmaChooseOrder;
use Magento\Rma\Test\Constraint\AssertRmaSuccessSaveMessage;
use Mtf\Fixture\FixtureFactory;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Class CreateRmaEntityTest
 * Test create Rma entity
 *
 * Preconditions:
 * 1. Enable RMA on Frontend (Configuration - Sales - RMA Settings)
 * 2. Create product
 * 3. Create Order
 * 4. Create invoice and Shipping
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Sales -> Returns
 * 3. Create New return
 * 4. Fill data according to dataSet
 * 5. Submit returns
 * 6. Perform all assertions
 *
 * @group RMA_(CS)
 * @ZephyrId MAGETWO-28571
 */
class CreateRmaEntityTest extends Injectable
{
    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Rma Index page on backend
     *
     * @var RmaIndex
     */
    protected $rmaIndex;

    /**
     * Rma choose order page on backend
     *
     * @var RmaChooseOrder
     */
    protected $rmaChooseOrder;

    /**
     * Rma choose order page on backend
     *
     * @var RmaNew
     */
    protected $rmaNew;

    /**
     * Prepare data
     *
     * @param ObjectManager $objectManager
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(ObjectManager $objectManager, FixtureFactory $fixtureFactory)
    {
        $this->objectManager = $objectManager;
        $this->fixtureFactory = $fixtureFactory;

        $configData = $this->fixtureFactory->createByCode('configData', ['dataSet' => 'rma_enable_on_frontend']);
        $configData->persist();
    }

    /**
     * Inject data
     *
     * @param RmaIndex $rmaIndex
     * @param RmaChooseOrder $rmaChooseOrder
     * @param RmaNew $rmaNew
     * @return void
     */
    public function __inject(
        RmaIndex $rmaIndex,
        RmaChooseOrder $rmaChooseOrder,
        RmaNew $rmaNew
    ) {
        $this->rmaIndex = $rmaIndex;
        $this->rmaChooseOrder = $rmaChooseOrder;
        $this->rmaNew = $rmaNew;
    }

    /**
     * Run test create Rma Entity
     *
     * @param Rma $rma
     * @param RmaIndex $rmaIndex
     * @param AssertRmaSuccessSaveMessage $assertRmaSuccessSaveMessage
     * @return array
     */
    public function test(Rma $rma, RmaIndex $rmaIndex, AssertRmaSuccessSaveMessage $assertRmaSuccessSaveMessage)
    {
        // Preconditions
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $this->objectManager->create(
            '\Magento\Sales\Test\TestStep\CreateInvoiceStep',
            ['order' => $order]
        )->run();
        $this->objectManager->create(
            '\Magento\Sales\Test\TestStep\CreateShipmentStep',
            ['order' => $order]
        )->run();

        // Steps
        $this->rmaIndex->open();
        $this->rmaIndex->getGridPageActions()->addNew();
        $this->rmaChooseOrder->getOrderGrid()->searchAndOpen(['id' => $rma->getOrderId()]);
        $this->rmaNew->getRmaForm()->fill($rma);
        $this->rmaNew->getPageActions()->save();

        $assertRmaSuccessSaveMessage->processAssert($rmaIndex);

        $rmaId = $this->getRmaId($rma);
        $rma = $this->createRma($rma, ['entity_id' => $rmaId]);
        return ['rma' => $rma];
    }

    /**
     * Get rma id
     *
     * @param Rma $rma
     * @return string
     */
    protected function getRmaId(Rma $rma)
    {
        $orderId = $rma->getOrderId();
        $filter = [
            'order_id_from' => $orderId,
            'order_id_to' => $orderId,
        ];

        $this->rmaIndex->open();
        $this->rmaIndex->getRmaGrid()->sortGridByField('increment_id', 'desc');
        $this->rmaIndex->getRmaGrid()->search($filter);

        $rowsData = $this->rmaIndex->getRmaGrid()->getRowsData(['number']);
        return $rowsData[0]['number'];
    }

    /**
     * Create products
     *
     * @param string $products
     */
    protected function prepareProducts($products)
    {
        $createProductsStep = ObjectManager::getInstance()->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        );
        $result = $createProductsStep->run();

        return $result['products'];
    }

    /**
     * Create rma entity
     *
     * @param Rma $rma
     * @param array $data
     * @return Rma
     */
    protected function createRma(Rma $rma, array $data)
    {
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $store = $order->getDataFieldConfig('store_id')['source']->getStore();

        $orderData = $order->getData();
        $orderData['store_id'] = ['data' => $store->getData()];
        $orderData['entity_id'] = ['value' => $order->getEntityId()['products']];
        $orderData['customer_id'] = ['customer' => $order->getDataFieldConfig('customer_id')['source']->getCustomer()];

        $rmaData = $rma->getData();
        $rmaData['order_id'] = ['data' => $orderData];
        $rmaData['items'] = ['data' => $rmaData['items']];
        $rmaData = array_replace_recursive($rmaData, $data);

        return $this->fixtureFactory->createByCode('rma', ['data' => $rmaData]);
    }
}
