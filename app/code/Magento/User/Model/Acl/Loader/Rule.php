<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Model\Acl\Loader;

class Rule implements \Magento\Acl\LoaderInterface
{
    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_resource;

    /**
     * @param \Magento\Core\Model\Acl\RootResource $rootResource
     * @param \Magento\Core\Model\Resource $resource
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter):
     */
    public function __construct(
        \Magento\Core\Model\Acl\RootResource $rootResource,
        \Magento\Core\Model\Resource $resource,
        array $data = array()
    ) {
        $this->_resource = $resource;
        $this->_rootResource = $rootResource;
    }

    /**
     * Populate ACL with rules from external storage
     *
     * @param \Magento\Acl $acl
     */
    public function populateAcl(\Magento\Acl $acl)
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
