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
 * Preconditions:
 * 1. Enable RMA on Frontend (Configuration - Sales - RMA Settings).
 * 2. Create product.
 * 3. Create Order.
 * 4. Create invoice and shipping.
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Sales -> Returns.
 * 3. Create new return.
 * 4. Fill data according to dataSet.
 * 5. Submit returns.
 * 6. Perform all assertions.
 *
 * @group RMA_(CS)
 * @ZephyrId MAGETWO-28571
 */
class CreateRmaEntityOnBackendTest extends Injectable
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Rma index page on backend.
     *
     * @var RmaIndex
     */
    protected $rmaIndex;

    /**
     * Rma choose order page on backend.
     *
     * @var RmaChooseOrder
     */
    protected $rmaChooseOrder;

    /**
     * Rma choose order page on backend.
     *
     * @var RmaNew
     */
    protected $rmaNew;

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;

        $this->objectManager->create(
            '\Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'rma_enable_on_frontend']
        )->run();
    }

    /**
     * Inject data.
     *
     * @param RmaIndex $rmaIndex
     * @param RmaChooseOrder $rmaChooseOrder
     * @param RmaNew $rmaNew
     * @return void
     */
    public function __inject(RmaIndex $rmaIndex, RmaChooseOrder $rmaChooseOrder, RmaNew $rmaNew)
    {
        $this->rmaIndex = $rmaIndex;
        $this->rmaChooseOrder = $rmaChooseOrder;
        $this->rmaNew = $rmaNew;
    }

    /**
     * Run test create Rma Entity.
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
     * Get rma id.
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

        $this->rmaIndex->getRmaGrid()->sortGridByField('increment_id', 'desc');
        $this->rmaIndex->getRmaGrid()->search($filter);

        $rowsData = $this->rmaIndex->getRmaGrid()->getRowsData(['number']);
        return $rowsData[0]['number'];
    }

    /**
     * Create rma entity.
     *
     * @param Rma $rma
     * @param array $data
     * @return Rma
     */
    protected function createRma(Rma $rma, array $data)
    {
        $rmaData = $rma->getData();
        $rmaData['order_id'] = ['data' => $this->getOrderData($rma)];
        $rmaData['items'] = ['data' => $rmaData['items']];
        $rmaData = array_replace_recursive($rmaData, $data);

        return $this->fixtureFactory->createByCode('rma', ['data' => $rmaData]);
    }

    /**
     * Return order data of rma entity.
     *
     * @param Rma $rma
     * @return array
     */
    protected function getOrderData(Rma $rma)
    {
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $store = $order->getDataFieldConfig('store_id')['source']->getStore();

        $data = $order->getData();
        $data['store_id'] = ['data' => $store->getData()];
        $data['entity_id'] = ['value' => $order->getEntityId()['products']];
        $data['customer_id'] = ['customer' => $order->getDataFieldConfig('customer_id')['source']->getCustomer()];

        return $data;
    }
}
