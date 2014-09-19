<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Sales\TaxRule;

/**
 * Class Grid
 * Coupons report grid
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'tax' => [
            'selector' => '.col-code',
        ],
        'rate' => [
            'selector' => '.col-rate'
        ],
        'orders' => [
            'selector' => '.col-orders_count'
        ],
        'tax_amount' => [
            'selector' => '.col-tax-amount'
        ]
    ];
}
