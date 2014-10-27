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
            'selector' => '.col.id',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'date' => [
            'selector' => '.col.date',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'ship_from' => [
            'selector' => '.col.shipping',
            'strategy' => Locator::SELECTOR_CSS
        ],
        'status' => [
            'selector' => '.col.status',
            'strategy' => Locator::SELECTOR_CSS
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
            $data[$name] = $this->_rootElement->find($locator['selector'], $locator['strategy'])->getText();
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
