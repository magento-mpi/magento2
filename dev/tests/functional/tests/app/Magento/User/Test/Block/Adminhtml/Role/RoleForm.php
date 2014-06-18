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
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Class RoleForm
 * Role edit form page
 */
class RoleForm extends FormTabs
{
    /**
     * Method for filling role info on role edit page
     *
     * @param AdminUserRole $role
     * @param string|null $username
     * @return void
     */
    public function fillRole(AdminUserRole $role, $username = null)
    {
        if ($username == null) {
            parent::fill($role);
        } else {
            $this->fill($role);
            $this->openTab('roles_users');
            $tabElement = $this->getTabElement('roles_users');
            $tabElement->fillFormTab(['username' => $username]);
        }
    }

    /**
     * Fill form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tabName => $tabFields) {
            if ($tabName == 'roles_users') {
                break;
            }
            $tabElement = $this->getTabElement($tabName);
            $this->openTab($tabName);
            $tabElement->fillFormTab(array_merge($tabFields, $this->unassignedFields), $this->_rootElement);
            $this->updateUnassignedFields($tabElement);
        }
        if (!empty($this->unassignedFields)) {
            $this->fillMissedFields($tabs);
        }

        return $this;
    }
}
