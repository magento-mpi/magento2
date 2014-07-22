<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Authorization;

use Magento\Framework\Authorization\RoleLocator;

class CompositeRoleLocator implements \Magento\Framework\Authorization\RoleLocator
{
    /**
     * @var RoleLocator[]
     */
    protected $roleLocators = [];

    /**
     * Register role locators.
     *
     * @param RoleLocator[] $roleLocators
     */
    public function __construct($roleLocators = [])
    {
        foreach ($roleLocators as $roleLocator) {
            $this->add($roleLocator);
        }
    }

    /**
     * Add role locator.
     *
     * @param RoleLocator $roleLocator
     * @return CompositeRoleLocator
     */
    public function add(RoleLocator $roleLocator)
    {
        $this->roleLocators[] = $roleLocator;
        return $this;
    }

    /**
     * Retrieve current role.
     *
     * @return string
     */
    public function getAclRoleId()
    {
        /** @var RoleLocator $roleLocator */
        foreach ($this->roleLocators as $roleLocator) {
            $aclRoleId = $roleLocator->getAclRoleId();
            if ($aclRoleId) {
                return $aclRoleId;
            }
        }
        return '';
    }
}