<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns\History\RmaTable;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class RmaRow
 * Rma row in table.
 */
class RmaRow extends Block
{
    /**
     * Mapping fields.
     *
     * @var array
     */
    protected $fields = [
        'entity_id' => [
            'selector' => '.col.id'
        ],
        'date' => [
            'selector' => '.col.date'
        ],
        'ship_from' => [
            'selector' => '.col.shipping'
        ],
        'status' => [
            'selector' => '.col.status'
        ]
    ];

    /**
     * Locator for action "View Return".
     *
     * @var string
     */
    protected $actionView = '.view';

    /**
     * Get data of rma row.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        foreach ($this->fields as $name => $locator) {
            $data[$name] = $this->_rootElement->find(
                $locator['selector'],
                isset($locator['strategy']) ? $locator['strategy'] : Locator::SELECTOR_CSS
            )->getText();
        }

        return $data;
    }

    /**
     * Click in action link "View Return".
     *
     * @return void
     */
    public function clickView()
    {
        $this->_rootElement->find($this->actionView)->click();
    }
}
