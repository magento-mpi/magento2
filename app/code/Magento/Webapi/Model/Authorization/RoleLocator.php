<?php
/**
 * API ACL RoleLocator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization;

class RoleLocator implements \Magento\Authorization\RoleLocator
{
    /**
     * @var string|null
     */
    protected $_roleId = null;

    /**
     * Initialize role ID.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_roleId = isset($data['roleId']) ? $data['roleId'] : null;
    }

    /**
     * Set role ID into role locator.
     *
     * @param string $roleId
     */
    public function setRoleId($roleId)
    {
        $this->_roleId = $roleId;
    }

    /**
     * Retrieve current role.
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        return $this->_roleId;
    }
}
