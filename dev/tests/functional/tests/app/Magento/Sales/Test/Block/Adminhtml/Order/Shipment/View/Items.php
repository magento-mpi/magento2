<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Shipment\View;

use Magento\Sales\Test\Block\Adminhtml\Order\AbstractItems;

/**
 * Class Items
 * Shipment Items block on Shipment view page
 */
class Items extends AbstractItems
{
    /**
     * Get items data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->_rootElement->find($this->rowItem)->getElements();
        $data = [];

        foreach ($items as $item) {
            $itemData = [];

            $itemData += $this->parseProductName($item->find($this->product)->getText());
            $itemData['qty'] = $item->find($this->qty)->getText();

            $data[] = $itemData;
        }

        return $data;
    }
}
