<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Authorization;

class RoleLocator implements \Magento\Framework\Authorization\RoleLocator
{
    /**
     * Authentication service
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * @param \Magento\Backend\Model\Auth\Session $session
     */
    public function __construct(\Magento\Backend\Model\Auth\Session $session)
    {
        $this->_session = $session;
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        if ($this->_session->hasUser()) {
            return $this->_session->getUser()->getAclRole();
        }
        return null;
    }
}
