<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Sales\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Order
 */
class Order implements SetupInterface
{

    /**
     * @var \Magento\Tools\SampleData\Module\Sales\Setup\Order\Converter
     */
    protected $converter;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Phrase\Renderer\CompositeFactory
     */
    protected $rendererCompositeFactory;

    /**
     * @var \Magento\Sales\Model\AdminOrder\CreateFactory
     */
    protected $createOrderFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoaderFactory
     */
    protected $invoiceLoaderFactory;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoaderFactory
     */
    protected $shipmentLoaderFactory;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory
     */
    protected $creditmemoLoaderFactory;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Order\Converter $converter
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Phrase\Renderer\CompositeFactory $rendererCompositeFactory
     * @param \Magento\Sales\Model\AdminOrder\CreateFactory $createOrderFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Backend\Model\Session\QuoteFactory $sessionQuoteFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoaderFactory $invoiceLoaderFactory
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoaderFactory $shipmentLoaderFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory $creditmemoLoaderFactory
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Tools\SampleData\Module\Sales\Setup\Order\Converter $converter,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Phrase\Renderer\CompositeFactory $rendererCompositeFactory,
        \Magento\Sales\Model\AdminOrder\CreateFactory $createOrderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Backend\Model\Session\QuoteFactory $sessionQuoteFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoaderFactory $invoiceLoaderFactory,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoaderFactory $shipmentLoaderFactory,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory $creditmemoLoaderFactory,
        $fixtures = [
            'Sales/orders.csv'
        ]
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->converter = $converter;
        $this->coreRegistry = $coreRegistry;
        $this->rendererCompositeFactory = $rendererCompositeFactory;
        $this->createOrderFactory = $createOrderFactory;
        $this->customerFactory = $customerFactory;
        $this->sessionQuoteFactory = $sessionQuoteFactory;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceLoaderFactory = $invoiceLoaderFactory;
        $this->shipmentLoaderFactory = $shipmentLoaderFactory;
        $this->creditmemoLoaderFactory = $creditmemoLoaderFactory;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing orders\n";
        $this->setPhraseRenderer();
        foreach ($this->fixtures as $file) {
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $orderData = $this->converter->convertRow($row);
                if (!empty($orderData)) {
                    $orderCreateModel = $this->processQuote($orderData);
                    if (!empty($orderData['payment'])) {
                        $orderCreateModel->setPaymentData($orderData['payment']);
                        $orderCreateModel->getQuote()->getPayment()->addData($orderData['payment']);
                    }
                    $customer = $this->customerFactory->create()
                        ->setWebsiteId(1)
                        ->loadByEmail($orderData['order']['account']['email']);
                    $orderCreateModel->getQuote()->setCustomer($customer);
                    $orderCreateModel->getSession()->setCustomerId($customer->getId());
                    $order = $orderCreateModel
                        ->importPostData($orderData['order'])
                        ->createOrder();
                    $transactionOrder = $this->getOrderItemForTransaction($order);
                    $this->invoiceOrder($transactionOrder);
                    $this->shipOrder($transactionOrder);
                    if ($orderData['refund'] === "yes") {
                        $this->refundOrder($transactionOrder, $order->getBaseGrandTotal());
                    }
                    $this->destroyRegistryData();
                    echo '.';
                }
            }
        }
        echo "\n";
    }

    /**
     * @param array $data
     * @return \Magento\Sales\Model\AdminOrder\Create
     */
    protected function processQuote($data = array())
    {
        $orderCreateModel = $this->createOrderFactory->create(
            [
                'quoteSession' => $this->sessionQuoteFactory->create()
            ]
        );
        if (!empty($data['order'])) {
            $orderCreateModel->importPostData($data['order']);
        }
        $orderCreateModel->getBillingAddress();
        $orderCreateModel->setShippingAsBilling(true);
        if (!empty($data['add_products'])) {
            $orderCreateModel->addProducts($data['add_products']);
        }
        $orderCreateModel->collectShippingRates();
        if (!empty($data['payment'])) {
            $orderCreateModel->getQuote()->getPayment()->addData($data['payment']);
        }
        $orderCreateModel->initRuleData()->saveQuote();
        return $orderCreateModel;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    protected function getOrderItemForTransaction(\Magento\Sales\Model\Order $order)
    {
        $order->getItemByQuoteItemId($order->getData('quote')->getId());
        foreach ($order->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return void
     */
    protected function invoiceOrder(\Magento\Sales\Model\Order\Item $orderItem)
    {
        $invoiceTransaction = null;
        $invoiceLoader = $this->invoiceLoaderFactory->create();
        $invoiceData = [$orderItem->getId() => $orderItem->getQtyToInvoice()];
        $invoice = $invoiceLoader->load($orderItem->getOrderId(), null, $invoiceData);
        if ($invoice) {
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            $invoiceTransaction = $this->transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $invoiceTransaction->save();
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return void
     */
    protected function shipOrder(\Magento\Sales\Model\Order\Item $orderItem)
    {
        $shipmentTransaction = null;
        $shipmentLoader = $this->shipmentLoaderFactory->create();
        $shipmentData = [$orderItem->getId() => $orderItem->getQtyToShip()];
        $shipmentLoader->setOrderId($orderItem->getOrderId());
        $shipmentLoader->setShipment($shipmentData);
        $shipment = $shipmentLoader->load();
        if ($shipment) {
            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);
            $shipmentTransaction = $this->transactionFactory->create()
                ->addObject($shipment)
                ->addObject($shipment->getOrder());
            $shipmentTransaction->save();
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param string $storeCreditAmount
     * @return void
     * @TODO uncomment code after merge with mainline
     */
    protected function refundOrder(\Magento\Sales\Model\Order\Item $orderItem, $storeCreditAmount = '')
    {
        $creditmemoTransaction = null;
        $creditmemoData = [
            $orderItem->getId() => $orderItem->getQtyToRefund()
        ];
        if (!empty($storeCreditAmount)) {
            //Refund to store credit doesn't working in Magento MAGETWO-30058
            //$creditmemoData['refund_customerbalance_return_enable'] = '1';
            //$creditmemoData['refund_customerbalance_return'] = '32';
        }
        $creditmemoLoader = $this->creditmemoLoaderFactory->create();
        $creditmemoLoader->setOrderId($orderItem->getOrderId());
        $creditmemoLoader->setCreditmemo($creditmemoData);
        $creditmemo = $creditmemoLoader->load();
        if ($creditmemo && $creditmemo->isValidGrandTotal()) {
            $creditmemo->setOfflineRequested(true);
            $creditmemo->register();
            $transactionSave = $this->transactionFactory->create()
                ->addObject($creditmemo)
                ->addObject($creditmemo->getOrder());
            $transactionSave->save();
        }
    }

    /**
     * Set phrase renderer
     * @return void
     */
    protected function setPhraseRenderer()
    {
        \Magento\Framework\Phrase::setRenderer($this->rendererCompositeFactory->create());
    }

    /**
     * Remove order create model
     * @return void
     */
    protected function destroyRegistryData()
    {
        if ($this->coreRegistry) {
            $this->coreRegistry->__destruct();
        }
    }
}