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
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_session = isset($data['session']) ?
            $data['session'] :
            Mage::getSingleton('Mage_Backend_Model_Auth_Session');
    }

    /**
     * Retrieve current role
     *
     * @return string
     */
    public function getAclRoleId()
    {
        $this->_session->getUser()->getAclRole();
    }
}
