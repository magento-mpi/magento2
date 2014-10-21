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
 * Class RmaItems
 * Rma items
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
          'selector' => '.col.sku'
        ],
        'condition' => [
            'selector' => '.col.condition'
        ],
        'resolution' => [
            'selector' => '.col.resolution'
        ],
        'qty_requested' => [
            'selector' => '.col.qty.request'
        ],
        'qty' => [
            'selector' => '.col.qty'
        ],
        'status' => [
            'selector' => '.col.status'
        ],
    ];

    /**
     * Locator for item row.
     *
     * @var string
     */
    protected $itemRow = 'tbody tr';

    /**
     * Get data of rma items
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->_rootElement->find($this->itemRow)->getElements();
        $data = [];

        foreach ($items as $key => $item) {
            foreach ($this->rowFields as $name => $locator) {
                $value = $item->find(
                    $locator['selector'],
                    isset($locator['strategy']) ? $locator['strategy'] : Locator::SELECTOR_CSS
                )->getText();
                $data[$key][$name] = trim($value);
            }
        }

        return $data;
    }
}
