<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Selection
 * Assigned product row to bundle option
 *
 */
class Selection extends Block
{
    /**
     * Fields mapping
     *
     * @var array
     */
    protected $mapping = array();

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->mapping = [
            'selection_price_value' => [
                'selector' => "[name$='[selection_price_value]']",
                'type' => 'input',

            ],
            'selection_price_type' => [
                'selector' => "[name$='[selection_price_type]']",
                'type' => 'select',

            ],
            'selection_qty' => [
                'selector' => "[name$='[selection_qty]']",
                'type' => 'input',

            ],
        ];
    }

    /**
     * Fill data to product row
     *
     * @param array $fields
     */
    public function fillProductRow(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (isset($this->mapping[$key])) {
                $this->_rootElement->find(
                    $this->mapping[$key]['selector'],
                    Locator::SELECTOR_CSS,
                    $this->mapping[$key]['type']
                )->setValue($value);
            }
        }
    }
}
