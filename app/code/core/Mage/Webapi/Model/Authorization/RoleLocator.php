<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api Acl RoleLocator
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization_RoleLocator implements Magento_Authorization_RoleLocator
{
    /**
     * @var string|null
     */
    protected $_roleId = null;

    /**
     * Initialize role id
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_roleId = isset($data['roleId']) ? $data['roleId'] : null;
    }

    /**
     * Set role id into role locator
     *
     * @param string $roleId
     */
    public function setRoleId($roleId)
    {
        $this->_roleId = $roleId;
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        return $this->_roleId;
    }
}
