<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\User;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Mtf\Client\Element;

/**
 * Class Edit
 * User edit form page
 */
class Edit extends FormTabs
{
    /**
     * Method for filling different fixtures' data on different tabs
     *
     * @param AdminUserInjectable $user
     * @param null $role
     */
    public function fillTabs(AdminUserInjectable $user, $role = null)
    {
        if ($user->hasData()) {
            parent::fill($user);
        }
        if ($role != null) {
            $this->openTab('user-role');
            $tabElement = $this->getTabElement('user-role');
            $tabElement->fillFormTab(['role_name' => $role->getRoleName()]);
        }
    }

    /**
     * Selecting user role on user role tab.
     *
     * @param string $role
     */
    public function fillUserRole($role)
    {
        $this->openTab('user-role');
        $tabElement = $this->getTabElement('user-role');
        $tabElement->fillFormTab(['role_name' => $role]);
    }
}