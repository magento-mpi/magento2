<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Block\Adminhtml\Rule;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Adminhtml Tax Rules managment grid
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule
 */
class Grid extends GridInterface
{
    /**
     * Locator value for opening needed row
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-code]';

    /**
     * Initialize block elements
     */
    protected $filters = [
        'code' => [
            'selector' => '#taxRuleGrid_filter_code',
        ],
        'customer_tax_class' => [
            'selector' => '#taxRuleGrid_filter_customer_tax_classes',
            'input' => 'select',
        ],
        'product_tax_class' => [
            'selector' => '#taxRuleGrid_filter_product_tax_classes',
            'input' => 'select',
        ],
        'tax_rate' => [
            'selector' => '#taxRuleGrid_filter_tax_rates',
            'input' => 'select',
        ],
    ];

    /**
     * Check if specific row exists in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = false)
    {
        $this->search(array('code' => $filter['code']));
        return parent::isRowVisible($filter, $isSearchable);
    }
}
