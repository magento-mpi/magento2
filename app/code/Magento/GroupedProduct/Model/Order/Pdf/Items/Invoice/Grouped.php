<?php
/**
 * Sales Order Invoice Pdf grouped items renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Order\Pdf\Items\Invoice;

class Grouped extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice
{
    /**
     * Draw process
     */
    public function draw()
    {
        $type = $this->getItem()->getOrderItem()->getRealProductType();
        $renderer = $this->getRenderedModel()->getRenderer($type);
        $renderer->setOrder($this->getOrder());
        $renderer->setItem($this->getItem());
        $renderer->setPdf($this->getPdf());
        $renderer->setPage($this->getPage());

        $renderer->draw();
    }
}
