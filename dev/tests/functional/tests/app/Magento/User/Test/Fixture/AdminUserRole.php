<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class AdminUserRole
 *
 * @package Magento\User\Test\Fixture
 */
class AdminUserRole extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\User\Test\Repository\AdminUserRole';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\User\Test\Handler\AdminUserRole\AdminUserRoleInterface';

    protected $defaultDataSet = [
        'gws_is_all' => null,
    ];

    protected $role_id = [
        'attribute_code' => 'role_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $parent_id = [
        'attribute_code' => 'parent_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $tree_level = [
        'attribute_code' => 'tree_level',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $role_type = [
        'attribute_code' => 'role_type',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $user_id = [
        'attribute_code' => 'user_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $role_name = [
        'attribute_code' => 'role_name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $user_type = [
        'attribute_code' => 'user_type',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gws_is_all = [
        'attribute_code' => 'gws_is_all',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $gws_websites = [
        'attribute_code' => 'gws_websites',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gws_store_groups = [
        'attribute_code' => 'gws_store_groups',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $all = [
        'attribute_code' => 'all',
    ];

    protected $roles_resources = [
    ];

    public function getRoleId()
    {
        return $this->getData('role_id');
    }

    public function getParentId()
    {
        return $this->getData('parent_id');
    }

    public function getTreeLevel()
    {
        return $this->getData('tree_level');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getRoleType()
    {
        return $this->getData('role_type');
    }

    public function getUserId()
    {
        return $this->getData('user_id');
    }

    public function getRoleName()
    {
        return $this->getData('role_name');
    }

    public function getUserType()
    {
        return $this->getData('user_type');
    }

    public function getGwsIsAll()
    {
        return $this->getData('gws_is_all');
    }

    public function getGwsWebsites()
    {
        return $this->getData('gws_websites');
    }

    public function getGwsStoreGroups()
    {
        return $this->getData('gws_store_groups');
    }

    public function getAll()
    {
        return $this->getData('all');
    }

    public function getRolesResources()
    {
        return $this->getData('roles_resources');
    }
}
