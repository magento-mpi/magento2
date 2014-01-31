<?php
/**
 * Uses ACL to control access. If ACL doesn't contain provided resource,
 * permission for all resources is checked
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization\Policy;

use Magento\Acl\Builder;
use Magento\Authorization\Policy;

class Acl implements Policy
{
    /**
     * @var \Magento\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * @param Builder $aclBuilder
     */
    public function __construct(Builder $aclBuilder)
    {
        $this->_aclBuilder = $aclBuilder;
    }

    /**
     * Check whether given role has access to give id
     *
     * @param string $roleId
     * @param string $resourceId
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($roleId, $resourceId, $privilege = null)
    {
        try {
            return $this->_aclBuilder->getAcl()->isAllowed($roleId, $resourceId, $privilege);
        } catch (\Exception $e) {
            try {
                if (!$this->_aclBuilder->getAcl()->has($resourceId)) {
                    return $this->_aclBuilder->getAcl()->isAllowed($roleId, null, $privilege);
                }
            } catch (\Exception $e) {
            }
        }
        return false;
    }
}
