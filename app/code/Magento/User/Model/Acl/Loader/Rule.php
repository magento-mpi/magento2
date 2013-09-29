<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_User_Model_Acl_Loader_Rule implements Magento_Acl_LoaderInterface
{
    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_resource;

    /**
     * @param Magento_Core_Model_Acl_RootResource $rootResource
     * @param Magento_Core_Model_Resource $resource
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter):
     */
    public function __construct(
        Magento_Core_Model_Acl_RootResource $rootResource,
        Magento_Core_Model_Resource $resource,
        array $data = array()
    ) {
        $this->_resource = $resource;
        $this->_rootResource = $rootResource;
    }

    /**
     * Populate ACL with rules from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $ruleTable = $this->_resource->getTableName("admin_rule");

        $adapter = $this->_resource->getConnection('core_read');

        $select = $adapter->select()
            ->from(array('r' => $ruleTable));

        $rulesArr = $adapter->fetchAll($select);

        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'] . $rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = !empty($rule['privileges']) ? explode(',', $rule['privileges']) : null;

            if ($acl->has($resource)) {
                if ($rule['permission'] == 'allow') {
                    if ($resource === $this->_rootResource->getId()) {
                        $acl->allow($role, null, $privileges);
                    }
                    $acl->allow($role, $resource, $privileges);
                } else if ($rule['permission'] == 'deny') {
                    $acl->deny($role, $resource, $privileges);
                }
            }
        }
    }
}
