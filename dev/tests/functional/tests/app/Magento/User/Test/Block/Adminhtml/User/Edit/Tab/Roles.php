<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\User\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Roles
 * Grid on Roles Tab page for User
 */
class Roles extends Grid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '.col-role_name';

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr';

    /**
     * Filters Name for Roles Grid
     *
     * @var array
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#permissionsUserRolesGrid_filter_assigned_user_role',
            'input' => 'select'
        ),
        'role_name' => array(
            'selector' => '#permissionsUserRolesGrid_filter_role_name'
        )
    );
}
