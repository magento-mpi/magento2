<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\User\Tab\Role;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 */
class Grid extends GridInterface
{
    /**
     * Grid filters' selectors
     *
     * @var array
     */
    protected $filters = [
        'role_name' => [
            'selector' => '#permissionsUserRolesGrid_filter_role_name'
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '.col-role_name';
}