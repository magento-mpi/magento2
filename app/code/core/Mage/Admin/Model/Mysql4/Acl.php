<?php

/**
 * Resource model for admin ACL
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Admin_Model_Mysql4_Acl
{
    /**
     * Read resource connection
     *
     * @var mixed
     */
    protected $_read;

    /**
     * Write resource connection
     *
     * @var mixed
     */
    protected $_write;

    /**
     * Initialize resource connections
     *
     */
    function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('admin_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('admin_write');
    }

    /**
     * Load ACL for the user
     *
     * @param integer $userId
     * @return Mage_Admin_Model_Acl
     */
    function loadAcl()
    {
        $acl = Mage::getModel('admin/acl');

        Mage::getSingleton('admin/config')->loadAclResources($acl);

        $roleTable = Mage::getSingleton('core/resource')->getTableName('admin/role');
        $rolesArr = $this->_read->fetchAll("select * from $roleTable order by tree_level");
        $this->loadRoles($acl, $rolesArr);

        $ruleTable = Mage::getSingleton('core/resource')->getTableName('admin/rule');
        $assertTable = Mage::getSingleton('core/resource')->getTableName('admin/assert');
        $rulesArr = $this->_read->fetchAll("select r.*, a.assert_type, a.assert_data
            from $ruleTable r left join $assertTable a on a.assert_id=r.assert_id");
        $this->loadRules($acl, $rulesArr);

        return $acl;
    }

    /**
     * Load roles
     *
     * @param Mage_Admin_Model_Acl $acl
     * @param array $rolesArr
     * @return Mage_Admin_Model_Mysql4_Acl
     */
    function loadRoles(Mage_Admin_Model_Acl $acl, array $rolesArr)
    {
        foreach ($rolesArr as $role) {
            $parent = $role['parent_id']>0 ? Mage_Admin_Model_Acl::ROLE_TYPE_GROUP.$role['parent_id'] : null;

            switch ($role['role_type']) {
                case Mage_Admin_Model_Acl::ROLE_TYPE_GROUP:
                    $roleId = $role['role_type'].$role['role_id'];
                    $acl->addRole(Mage::getModel('admin/acl_role_group', $roleId), $parent);
                    break;

                case Mage_Admin_Model_Acl::ROLE_TYPE_USER:
                    $roleId = $role['role_type'].$role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(Mage::getModel('admin/acl_role_user', $roleId), $parent);
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }
                    break;
            }
        }

        return $this;
    }

    /**
     * Load rules
     *
     * @param Mage_Admin_Model_Acl $acl
     * @param array $rulesArr
     * @return Mage_Admin_Model_Mysql4_Acl
     */
    function loadRules(Mage_Admin_Model_Acl $acl, array $rulesArr)
    {
        #$acl->allow('G2', null, null, null);// FIXME
        #$acl->allow('G1', null, null, null);// FIXME
    	foreach ($rulesArr as $rule) {
            $role = $rule['role_type'].$rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = !empty($rule['privileges']) ? explode(',', $rule['privileges']) : null;

            $assert = null;
            if (0!=$rule['assert_id']) {
                $assertClass = Mage::getSingleton('admin/config')->getAclAssert($rule['assert_type'])->getClassName();
                $assert = new $assertClass(unserialize($rule['assert_data']));
            }
            if ( $rule['permission'] == 'allow' ) {
            	$acl->allow($role, $resource, $privileges, $assert);
            } else if ( $rule['permission'] == 'deny' ) {
            	$acl->deny($role, $resource, $privileges, $assert);
            }
            /*
            switch ($rule['permission']) {
                case Mage_Admin_Model_Acl::RULE_PERM_ALLOW:
                    $acl->allow($role, $resource, $privileges, $assert);
                    break;

                case Mage_Admin_Model_Acl::RULE_PERM_DENY:
                    $acl->deny($role, $resource, $privileges, $assert);
                    break;
            }
            */
        }
        return $this;
    }

}
