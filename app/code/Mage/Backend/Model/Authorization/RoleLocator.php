<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Authorization_RoleLocator implements Magento_Authorization_RoleLocator
{
    /**
     * Authentication service
     *
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @param Mage_Backend_Model_Auth_Session $session
     */
    public function __construct(Mage_Backend_Model_Auth_Session $session)
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
