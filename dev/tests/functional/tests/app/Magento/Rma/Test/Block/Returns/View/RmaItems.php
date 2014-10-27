<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns\View;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Rma items.
 */
class RmaItems extends Block
{
    /**
     * Mapping row fields.
     *
     * @var array
     */
    protected $rowFields = [
        'sku' => [
            'selector' => '.col.sku',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'condition' => [
            'selector' => '.col.condition',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'resolution' => [
            'selector' => '.col.resolution',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'qty_requested' => [
            'selector' => '.col.qty.request',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'qty' => [
            'selector' => '.col.qty',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'status' => [
            'selector' => '.col.status',
            'strategy' => Locator::SELECTOR_CSS
        ],
    ];

    /**
     * Locator for item row.
     *
     * @var string
     */
    protected $itemRow = 'tbody tr';

    /**
     * Get data of rma items.
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->_rootElement->find($this->itemRow)->getElements();
        $data = [];

        foreach ($items as $key => $item) {
            foreach ($this->rowFields as $name => $locator) {
                $value = $item->find($locator['selector'], $locator['strategy'])->getText();
                $data[$key][$name] = trim($value);
            }
        }

        return $data;
    }
}
