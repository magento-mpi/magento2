<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use Magento\Framework\App\RequestInterface;

class InvoiceLoader
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var int
     */
    protected $orderId;
    /**
     * @var int
     */
    protected $invoiceId;
    /**
     * @var array
     */
    protected $invoiceItems;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
        $this->registry = $registry;
        $this->_objectManager = $objectManager;
    }

    /**
     * Set corresponding order Id
     *
     * @param $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * Set corresponding invoice Id
     *
     * @param $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }

    /**
     * Linear array of order items for invoice:
     *      [
     *          orderItemId => qtyInvoicedItems
     *      ]
     * @param $invoiceItems
     * @return $this
     */
    public function setInvoiceItems($invoiceItems)
    {
        $this->invoiceItems = $invoiceItems;
        return $this;
    }

    /**
     * Create invoice
     *
     * @return bool|\Magento\Sales\Model\Order\Invoice
     * @throws \Exception
     */
    public function create()
    {
        return $this->load($this->orderId, $this->invoiceId, $this->invoiceItems);
    }

    /**
     * Load invoice
     * @deprecated
     * @param int $orderId
     * @param null|int $invoiceId
     * @param array $invoiceItems
     * @return \Magento\Sales\Model\Order\Invoice | bool
     * @throws \Exception
     */
    public function load($orderId, $invoiceId = null, array $invoiceItems = [])
    {
        $invoice = false;
        if ($invoiceId) {
            $invoice = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
            if (!$invoice->getId()) {
                $this->messageManager->addError(__('The invoice no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->messageManager->addError(__('The order no longer exists.'));
                return false;
            }
            /**
             * Check invoice create availability
             */
            if (!$order->canInvoice()) {
                $this->messageManager->addError(__('The order does not allow an invoice to be created.'));
                return false;
            }

            $invoice = $this->_objectManager->create(
                'Magento\Sales\Model\Service\Order',
                array('order' => $order)
            )->prepareInvoice(
                $invoiceItems
            );
            if (!$invoice->getTotalQty()) {
                throw new \Exception(__('Cannot create an invoice without products.'));
            }
        }

        $this->registry->register('current_invoice', $invoice);
        return $invoice;
    }
}
