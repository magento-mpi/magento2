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
    /** @var Mage_Core_Service_Context */
    protected $_serviceContext;

    public function __construct(Mage_Core_Service_Context $context)
    {
        $this->_serviceContext = $context;
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        return $this->_serviceContext->getUser()->getAclRole();
    }
}
