<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default authorisation policy. Uses ACL to control access. If ACL doesn't contain provided resource,
 * permission for all resources is checked
 */
class Mage_Core_Model_Authorization_Policy implements Magento_Authorization_Policy
{
    /**
     * @var Magento_Acl
     */
    protected $_acl;

    /**
     * @param Magento_Acl $acl
     */
    public function __construct(Magento_Acl $acl)
    {
        $this->_acl = $acl;
    }

    /**
     * Check whether given role has access to give id
     *
     * @param string $roleId
     * @param string $resourceId
     * @param mixed $privilege
     * @return bool
     */
    public function isAllowed($roleId, $resourceId, $privilege = null)
    {
        try {
            return $this->_acl->isAllowed($roleId, $resourceId, $privilege);
        } catch (Exception $e) {
            try {
                if (!$this->_acl->has($resourceId)) {
                    return $this->_acl->isAllowed($roleId, null, $privilege);
                }
            } catch (Exception $e) {
            }
        }
        return false;
    }
}
