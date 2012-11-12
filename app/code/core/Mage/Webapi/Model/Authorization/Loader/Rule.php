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
 * Api Acl Rule Loader
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization_Loader_Rule implements Magento_Acl_Loader
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Rule
     */
    protected $_ruleResource;

    /**
     * @param Mage_Webapi_Model_Resource_Acl_Rule $ruleResource
     */
    public function __construct(Mage_Webapi_Model_Resource_Acl_Rule $ruleResource)
    {
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Populate ACL with rules from external storage
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
