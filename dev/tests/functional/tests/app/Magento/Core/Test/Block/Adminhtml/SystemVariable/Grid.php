<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Block\Adminhtml\SystemVariable;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Adminhtml System Variable management grid
 */
class Grid extends ParentGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-code]';

    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'code' => [
            'selector' => '#customVariablesGrid_filter_code',
        ],
        'name' => [
            'selector' => '#customVariablesGrid_filter_name',
        ],
    ];
}
