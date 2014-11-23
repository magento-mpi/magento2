<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class InvoiceMapper
 */
class InvoiceMapper
{
    /**
     * @var InvoiceBuilder
     */
    protected $invoiceBuilder;

    /**
     * @var InvoiceItemMapper
     */
    protected $invoiceItemMapper;

    /**
     * @param InvoiceBuilder $invoiceBuilder
     * @param InvoiceItemMapper $invoiceItemMapper
     */
    public function __construct(InvoiceBuilder $invoiceBuilder, InvoiceItemMapper $invoiceItemMapper)
    {
        $this->invoiceBuilder = $invoiceBuilder;
        $this->invoiceItemMapper = $invoiceItemMapper;
    }

    /**
     * Returns array of items
     *
     * @param \Magento\Sales\Model\Order\Invoice $object
     * @return InvoiceItem[]
     */
    protected function getItems(\Magento\Sales\Model\Order\Invoice $object)
    {
        $items = [];
        foreach ($object->getAllItems() as $item) {
            $items[] = $this->invoiceItemMapper->extractDto($item);
        }
        return $items;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $object
     * @return \Magento\Framework\Api\AbstractExtensibleObject
     */
    public function extractDto(\Magento\Sales\Model\Order\Invoice $object)
    {
        $this->invoiceBuilder->populateWithArray($object->getData());
        $this->invoiceBuilder->setItems($this->getItems($object));
        return $this->invoiceBuilder->create();
    }
}
