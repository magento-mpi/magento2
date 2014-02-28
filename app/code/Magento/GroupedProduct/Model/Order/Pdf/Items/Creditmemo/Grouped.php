<?php
/**
 * Sales Order Creditmemo Pdf grouped items renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Order\Pdf\Items\Creditmemo;

class Grouped extends \Magento\Sales\Model\Order\Pdf\Items\Creditmemo\DefaultCreditmemo
{
    /**
     * Draw process
     *
     * @return void
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
