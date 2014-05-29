<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

class Grid extends AbstractGrid
{
    /**
     * Locator value for name column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-name]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '.filter [name="name"]',
        ],
        'start_on_from' => [
            'selector' => '.filter [name="from_date[from]"]',
        ],
        'applies_to' => [
            'selector' => '.filter [name="apply_to"]',
            'input' => 'select',
        ],
        'status' => [
            'selector' => '.filter [name="is_active"]',
            'input' => 'select',
        ],
    ];
}
