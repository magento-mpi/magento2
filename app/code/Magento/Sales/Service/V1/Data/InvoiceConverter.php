<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

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
        return $this->invoiceLoader->load(
            $dataObject->getOrderId(),
            $dataObject->getEntityId(),
            $dataObject->getItems()
        );
    }
}
