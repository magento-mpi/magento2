<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\User\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Roles
 * Grid on Roles Tab page for User
 *
 */
class Roles extends Grid
{
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

    /**
     * Initialize grid elements
     */
    protected function _init()
    {
        parent::_init();
        $this->selectItem = 'tbody tr';
    }
}

