<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Selection
 * Assigned product row to bundle option
 *
 * @package Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option
 */
class Selection extends Block
{
    /**
     * Fields mapping
     *
     * @var array
     */
    private $_mapping = array();

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_mapping = array(
            'selection_price_value' => "[name*='[selection_price_value]']",
            'selection_price_type' => "[name*='[selection_price_type]']",
            'selection_qty' => "[name*='[selection_qty]']"
        );
    }

    /**
     * Fill data to product row
     *
     * @param array $fields
     */
    public function fillProductRow(array $fields)
    {
        foreach ($fields as $key => $field) {
            $typifiedElement = isset($field['input']) ? $field['input'] : null;
            $this->_rootElement->find($this->_mapping[$key], Locator::SELECTOR_CSS, $typifiedElement)
                ->setValue($field['value']);
        }
    }
}
