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
     * Load invoice
     *
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
