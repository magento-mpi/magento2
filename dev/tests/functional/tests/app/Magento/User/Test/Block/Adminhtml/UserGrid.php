<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

class UserGrid extends Grid
{
    /**
     * Grid filters' selectors
     *
     * @var array
     */
    protected $filters = [
        'username' => [
            'selector' => '#permissionsUserGrid_filter_username'
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '[data-column="username"]';
} 