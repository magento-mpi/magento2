<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\Role;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\User\Test\Fixture\AdminUserRole;

/**
 * Class Edit
 * Role edit form page
 */
class Edit extends FormTabs
{
    /**
     * Method for filling role info on role edit page
     *
     * @param AdminUserRole $role
     * @param string|null $username
     */
    public function fillRole(AdminUserRole $role, $username = null)
    {
        if ($username == null) {
            parent::fill($role);
        } else {
            $this->openTab('roles-users');
            $tabElement = $this->getTabElement('roles-users');
            $tabElement->fillFormTab(['username' => $username]);
        }
    }
}
