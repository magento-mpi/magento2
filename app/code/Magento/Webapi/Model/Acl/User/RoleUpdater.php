<?php
/**
 * User role in role grid items updater.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\User;

class RoleUpdater implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{
    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var \Magento\Webapi\Model\Acl\User\Factory
     */
    protected $_userFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Webapi\Model\Acl\User\Factory $userFactory
     */
    public function __construct(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Webapi\Model\Acl\User\Factory $userFactory
    ) {
        $this->_userId = (int)$request->getParam('user_id');
        $this->_userFactory = $userFactory;
    }

    /**
     * Initialize value with role assigned to user.
     *
     * @param int|null $value
     * @return int|null
     */
    public function update($value)
    {
        if ($this->_userId) {
            $value = $this->_userFactory->create()->load($this->_userId)->getRoleId();
        }
        return $value;
    }
}
