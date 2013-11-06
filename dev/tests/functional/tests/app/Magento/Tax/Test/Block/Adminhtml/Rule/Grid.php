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

namespace Magento\Tax\Test\Block\Adminhtml\Rule;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Tax rules grid
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule
 */
class Grid extends GridInterface
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => array(
                'selector' => '#taxRuleGrid_filter_code'
            ),
            'customer_tax_class' => array(
                'selector' => '#taxRuleGrid_filter_customer_tax_classes',
                'input' => 'select'
            ),
            'product_tax_class' => array(
                'selector' => '#taxRuleGrid_filter_product_tax_classes',
                'input' => 'select'
            ),
            'tax_rate' => array(
                'selector' => '#taxRuleGrid_filter_tax_rates',
                'input' => 'select'
            )
        );
    }

    /**
     * Check if specific row exists in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = false)
    {
//        $this->search(array('name' => $filter['name'])); TODO: remove comment after apply first pull request to MTF
        return parent::isRowVisible($filter, $isSearchable);
    }
}