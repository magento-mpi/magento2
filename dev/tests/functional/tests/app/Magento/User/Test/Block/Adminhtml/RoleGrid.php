<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class RoleGrid
 * Role grid on role index page
 *
 * @package Magento\User\Test\Block\Adminhtml
 */
class RoleGrid extends Grid
{
    /**
     * Grid filters' selectors
     *
     * @var array
     */
    protected $filters = [
        'id' => [
            'selector' => '#roleGrid_filter_role_id'
        ],
        'role_name' => [
            'selector' => '#roleGrid_filter_role_name'
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td';
}