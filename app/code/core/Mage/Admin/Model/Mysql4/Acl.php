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
     * All the group roles are prepended by G
     *
     */
    const ROLE_TYPE_GROUP = 'G';
    
    /**
     * All the user roles are prepended by U
     *
     */
    const ROLE_TYPE_USER = 'U';
    
    /**
     * Permission level to deny access
     *
     */
    const RULE_PERM_DENY = 0;
    
    /**
     * Permission level to inheric access from parent role
     *
     */
    const RULE_PERM_INHERIT = 1;
    
    /**
     * Permission level to allow access
     *
     */
    const RULE_PERM_ALLOW = 2;

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
    function loadUserAcl($userId)
    {
        $acl = Mage::getModel('admin/acl');
        
        Mage::getSingleton('admin/config')->loadAclResources($acl);

        $roleTable = Mage::getSingleton('core/resource')->getTableName('admin_resource', 'role');
        $rolesSelect = $this->_read->select()->from($roleTable)->order(array('tree_level'));
        $rolesArr = $this->_read->fetchAll($rolesSelect);
        $this->loadRoles($acl, $rolesArr);
        
        $ruleTable = Mage::getSingleton('core/resource')->getTableName('admin_resource', 'rule');
        $assertTable = Mage::getSingleton('core/resource')->getTableName('admin_resource', 'assert');
        $rulesSelect = $this->_read->select()->from($ruleTable)
            ->joinLeft($assertTable, "$assertTable.assert_id=$ruleTable.assert_id", array('assert_type', 'assert_data'));
        $rulesArr = $this->_read->fetchAll($rulesSelect);        
        $this->loadRules($acl, $rulesArr);
        
        return $acl;
    }
    
    /**
     * Load roles
     *
     * @param Zend_Acl $acl
     * @param array $rolesArr
     * @return Mage_Admin_Model_Mysql4_Acl
     */
    function loadRoles(Zend_Acl $acl, array $rolesArr)
    {
        foreach ($rolesArr as $role) {
            $parent = $role['parent_id']>0 ? self::ROLE_TYPE_GROUP.$role['parent_id'] : null;
            
            switch ($role['role_type']) {
                case self::ROLE_TYPE_GROUP:
                    $roleId = $role['role_type'].$role['role_id'];
                    $acl->addRole(Mage::getModel('admin/acl_role_group', $roleId), $parent);
                    break;
                    
                case self::ROLE_TYPE_USER:
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
     * @param Zend_Acl $acl
     * @param array $rulesArr
     * @return Mage_Admin_Model_Mysql4_Acl
     */
    function loadRules(Zend_Acl $acl, array $rulesArr)
    {
        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'].$rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = !empty($rule['privileges']) ? explode(',', $rule['privileges']) : null;

            $assert = null;
            if (0!=$rule['assert_id']) {
                $assertClass = Mage::getSingleton('admin/config')->getAclAssert($rule['assert_type'])->getClassName();
                $assert = new $assertClass(unserialize($rule['assert_data']));
            }
            switch ($rule['permission']) {
                case self::RULE_PERM_ALLOW:
                    $acl->allow($role, $resource, $privileges, $assert);
                    break;
                    
                case self::RULE_PERM_DENY:
                    $acl->deny($role, $resource, $privileges, $assert);
                    break;
            }
        }
        return $this;
    }

}