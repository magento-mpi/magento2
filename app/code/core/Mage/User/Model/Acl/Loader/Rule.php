<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Acl_Loader_Rule implements Magento_Acl_Loader
{
    public function __construct(array $data = array())
    {
        $this->_adapter = isset($data['adapter']) ? $data['adapter'] : Mage::getSingleton('Mage_Core_Model_Resource_Db_Abstract')
    }

    /**
     * Populate ACL with rules from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $assertTable = $this->getTable("admin_assert");
        $ruleTable = $this->getTable("admin_rule");

        $adapter = $this->_getReadAdapter();

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
            if (0 != $rule['assert_id']) {
                $assertClass = Mage::getSingleton('Mage_Admin_Model_Config')
                    ->getAclAssert($rule['assert_type'])
                    ->getClassName();
                $assert = new $assertClass(unserialize($rule['assert_data']));
            }
            try {
                if ( $rule['permission'] == 'allow' ) {
                    if ($resource === self::ACL_ALL_RULES) {
                        $acl->allow($role, null, $privileges, $assert);
                    }
                    $acl->allow($role, $resource, $privileges, $assert);
                } else if ( $rule['permission'] == 'deny' ) {
                    $acl->deny($role, $resource, $privileges, $assert);
                }
            } catch (Exception $e) {
                //$m = $e->getMessage();
                //if ( eregi("^Resource '(.*)' not found", $m) ) {
                // Deleting non existent resource rule from rules table
                //$cond = $this->_write->quoteInto('resource_id = ?', $resource);
                //$this->_write->delete(Mage::getSingleton('Mage_Core_Model_Resource')
                //    ->getTableName('admin_rule'), $cond);
                //} else {
                //TODO: We need to log such exceptions to somewhere like a system/errors.log
                //}
            }
            /*
            switch ($rule['permission']) {
                case Magento_Acl::RULE_PERM_ALLOW:
                    $acl->allow($role, $resource, $privileges, $assert);
                    break;

                case Magento_Acl::RULE_PERM_DENY:
                    $acl->deny($role, $resource, $privileges, $assert);
                    break;
            }
            */
        }
    }
}
