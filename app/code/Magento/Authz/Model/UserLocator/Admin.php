<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model\UserLocator;

use Magento\Authz\Model\UserLocatorInterface;
use Magento\Authz\Model\UserIdentifier;
use Magento\Backend\Model\Auth\Session as AdminSession;

/**
 * Admin user locator.
 */
class Admin implements UserLocatorInterface
{
    /**
     * @var AdminSession
     */
    protected $_adminSession;

    /**
     * Initialize dependencies.
     *
     * @param AdminSession $adminSession
     */
    public function __construct(AdminSession $adminSession)
    {
        $this->_adminSession = $adminSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->_adminSession->hasUser() ? (int)$this->_adminSession->getUser()->getId() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserType()
    {
        return UserIdentifier::USER_TYPE_ADMIN;
    }
}
