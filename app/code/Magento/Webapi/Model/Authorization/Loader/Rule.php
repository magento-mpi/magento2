<?php
/**
 * API ACL Rule Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Loader;

class Rule implements \Magento\Acl\LoaderInterface
{
    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Rule
     */
    protected $_ruleResource;

    /**
     * @param \Magento\Webapi\Model\Resource\Acl\Rule $ruleResource
     */
    public function __construct(\Magento\Webapi\Model\Resource\Acl\Rule $ruleResource)
    {
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Populate ACL with rules from external storage.
     *
     * @param \Magento\Acl $acl
     */
    public function populateAcl(\Magento\Acl $acl)
    {
        $ruleList = $this->_ruleResource->getRuleList();
        foreach ($ruleList as $rule) {
            $role = $rule['role_id'];
            $resource = $rule['resource_id'];
            if ($acl->hasRole($role) && $acl->has($resource)) {
                $acl->allow($role, $resource);
            }
        }
    }
}
