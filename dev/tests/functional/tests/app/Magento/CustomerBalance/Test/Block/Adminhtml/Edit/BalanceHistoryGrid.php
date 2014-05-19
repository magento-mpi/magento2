<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Edit;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class BalanceHistoryGrid
 * Balance history grid
 */
class BalanceHistoryGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'balance_action' => [
            'selector' => '[name="balance_action"]',
            'input' => 'select',
        ],
        'info' => [
            'selector' => '[name="additional_info"]',
        ],
    ];
}