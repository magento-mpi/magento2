<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Service_Acl_RoleLocator implements Magento_Authorization_RoleLocator
{
    /** @var Mage_Core_Service_Manager */
    protected $_serviceManager;

    public function __construct(Mage_Core_Service_Manager $manager)
    {
        $this->_serviceManager = $manager;
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        return $this->_serviceManager->getUser()->getAclRole();
    }
}
