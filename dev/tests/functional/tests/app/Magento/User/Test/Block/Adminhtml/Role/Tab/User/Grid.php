<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\Role\Tab\User;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

class Grid extends GridInterface
{
    /**
     * Grid filters' selectors
     *
     * @var array
     */
    protected $filters = [
        'username' => [
            'selector' => '#roleUserGrid_filter_role_user_username'
        ]
    ];

    /**
     * Locator value for role name column
     *
     * @var string
     */
    protected $editLink = '.col-role_user_username';
} 