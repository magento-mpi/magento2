<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Sales\Setup\Order;

/**
 * Class Processor
 */
class Processor
{
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
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Phrase\Renderer\CompositeFactory $rendererCompositeFactory
     * @param \Magento\Sales\Model\AdminOrder\CreateFactory $createOrderFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Backend\Model\Session\QuoteFactory $sessionQuoteFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoaderFactory $invoiceLoaderFactory
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoaderFactory $shipmentLoaderFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory $creditmemoLoaderFactory
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Phrase\Renderer\CompositeFactory $rendererCompositeFactory,
        \Magento\Sales\Model\AdminOrder\CreateFactory $createOrderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Backend\Model\Session\QuoteFactory $sessionQuoteFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoaderFactory $invoiceLoaderFactory,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoaderFactory $shipmentLoaderFactory,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory $creditmemoLoaderFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->rendererCompositeFactory = $rendererCompositeFactory;
        $this->createOrderFactory = $createOrderFactory;
        $this->customerFactory = $customerFactory;
        $this->sessionQuoteFactory = $sessionQuoteFactory;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceLoaderFactory = $invoiceLoaderFactory;
        $this->shipmentLoaderFactory = $shipmentLoaderFactory;
        $this->creditmemoLoaderFactory = $creditmemoLoaderFactory;
    }

    /**
     * @param array $orderData
     */
    public function createOrder($orderData)
    {
        $this->setPhraseRenderer();
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
            $registryItems = [
                'rule_data',
                'currently_saved_addresses',
                'current_invoice',
                'current_shipment'
            ];
            $this->unsetRegistryData($registryItems);
        }
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
            $creditmemoTransaction = $this->transactionFactory->create()
                ->addObject($creditmemo)
                ->addObject($creditmemo->getOrder());
            $creditmemoTransaction->save();
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
     * Unset registry item
     * @param array|string $unsetData
     * @return void
     */
    protected function unsetRegistryData($unsetData)
    {
        if (is_array($unsetData)) {
            foreach ($unsetData as $item) {
                $this->coreRegistry->unregister($item);
            }
        } else {
            $this->coreRegistry->unregister($unsetData);
        }
    }
}