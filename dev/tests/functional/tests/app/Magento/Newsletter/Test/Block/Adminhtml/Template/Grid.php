<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Block\Adminhtml\Template;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Newsletter templates grid block
 *
 * @package Magento\Newsletter\Test\Block\Aminhtml\Template
 */
class Grid extends AbstractGrid
{
    /**
     * Filters array mapping
     *
     * @var array $filters
     */
    protected $filters = [
        'code' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-text-1-filter-code"]'
        ]
    ];
}
