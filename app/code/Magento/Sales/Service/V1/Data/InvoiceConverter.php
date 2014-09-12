<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class InvoiceConverter
 * @package Magento\Sales\Service\V1\Data
 */
class InvoiceConverter
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader
     */
    protected $invoiceLoader;

    /**
     * @param \Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader $invoiceLoader
     */
    public function __construct(\Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader $invoiceLoader)
    {
        $this->invoiceLoader = $invoiceLoader;
    }

    /**
     * @param Invoice $dataObject
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Exception
     */
    public function getModel(Invoice $dataObject)
    {
        $items = [];
        /** @var InvoiceItem $item */
        foreach ($dataObject->getItems() as $item) {
            $items[$item->getOrderItemId()] = $item->getQty();
        }
        return $this->invoiceLoader
            ->setOrderId($dataObject->getOrderId())
            ->setInvoiceId($dataObject->getEntityId())
            ->setInvoiceItems($items)
            ->create();
    }
}
