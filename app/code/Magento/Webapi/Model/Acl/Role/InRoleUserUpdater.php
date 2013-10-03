<?php
/**
 * Users in role grid "In Role User" column with checkbox updater.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Role;

class InRoleUserUpdater implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{
    /**
     * @var int
     */
    protected $_roleId;

    /**
     * @var \Magento\Webapi\Model\Resource\Acl\User
     */
    protected $_userResource;

    /**
     * Constructor.
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Webapi\Model\Resource\Acl\User $userResource
     */
    public function __construct(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Webapi\Model\Resource\Acl\User $userResource
    ) {
        $this->_roleId = (int)$request->getParam('role_id');
        $this->_userResource = $userResource;
    }

    /**
     * Init values with users assigned to role.
     *
     * @param array|null $values
     * @return array|null
     */
    public function update($values)
    {
        if ($this->_roleId) {
            $values = $this->_userResource->getRoleUsers($this->_roleId);
        }
        return $values;
    }
}
