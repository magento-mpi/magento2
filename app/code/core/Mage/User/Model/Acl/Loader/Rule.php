<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Model_Acl_Loader_Rule implements Magento_Acl_Loader
{
    const ACL_ALL_RULES = 'all';

    /**
     * @var Mage_Core_Model_Resource
     */
    protected $_resource;

    public function __construct(array $data = array())
    {
        $this->_resource = isset($data['resource'])
            ? $data['resource']
            : Mage::getSingleton('Mage_Core_Model_Resource');
    }

    /**
     * Populate ACL with rules from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $assertTable = $this->_resource->getTableName("admin_assert");
        $ruleTable = $this->_resource->getTableName("admin_rule");

        $adapter = $this->_resource->getConnection('read');

        $select = $adapter->select()
            ->from(array('r' => $ruleTable))
            ->joinLeft(
                array('a' => $assertTable),
                'a.assert_id = r.assert_id',
                array('assert_type', 'assert_data')
            );

        $rulesArr = $adapter->fetchAll($select);

        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'] . $rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = !empty($rule['privileges']) ? explode(',', $rule['privileges']) : null;

            $assert = null;
            if ( $rule['permission'] == 'allow' ) {
                if ($resource === self::ACL_ALL_RULES) {
                    $acl->allow($role, null, $privileges, $assert);
                }
                $acl->allow($role, $resource, $privileges, $assert);
            } else if ( $rule['permission'] == 'deny' ) {
                $acl->deny($role, $resource, $privileges, $assert);
            }
        }
    }
}
