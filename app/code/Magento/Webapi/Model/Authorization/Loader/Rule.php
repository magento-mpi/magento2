<?php
/**
 * API ACL Rule Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Loader_Rule implements Magento_Acl_LoaderInterface
{
    /**
     * @var Magento_Webapi_Model_Resource_Acl_Rule
     */
    protected $_ruleResource;

    /**
     * @param Magento_Webapi_Model_Resource_Acl_Rule $ruleResource
     */
    public function __construct(Magento_Webapi_Model_Resource_Acl_Rule $ruleResource)
    {
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Populate ACL with rules from external storage.
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
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
