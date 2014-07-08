<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\User;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\User\Test\Fixture\AdminUserRole;
use Mtf\Client\Element;

/**
 * Class Edit
 * User edit form page
 */
class UserForm extends FormTabs
{
    /**
     * Method for filling different fixtures' data on different tabs
     *
     * @param AdminUserInjectable $user
     * @param null|AdminUserRole $role
     */
    public function fillUser(AdminUserInjectable $user, AdminUserRole $role = null)
    {
        if ($user->hasData()) {
            parent::fill($user);
        }
        if ($role != null) {
            $this->openTab('user-role');
            $tabElement = $this->getTabElement('user-role');
            $tabElement->fillFormTab(['rolename' => $role->getRoleName()]);
        }
    }
}
