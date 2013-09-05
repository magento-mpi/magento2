<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Authorization_RoleLocator implements \Magento\Authorization\RoleLocator
{
    /**
     * Authentication service
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @param Magento_Backend_Model_Auth_Session $session
     */
    public function __construct(Magento_Backend_Model_Auth_Session $session)
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
