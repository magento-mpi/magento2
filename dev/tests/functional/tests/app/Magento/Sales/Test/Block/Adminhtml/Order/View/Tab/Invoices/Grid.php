<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Invoices;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Invoices grid on order view page
 */
class Grid extends ParentGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'id' => [
            'selector' => 'input[name="increment_id"]'
        ],
        'status' => [
            'selector' => 'select[name="state"]',
            'input' => 'select'
        ],
        'amount_from' => [
            'selector' => 'input[name="base_grand_total[from]"]',
        ],
        'amount_to' => [
            'selector' => 'input[name="base_grand_total[to]"]'
        ]
    ];
}
