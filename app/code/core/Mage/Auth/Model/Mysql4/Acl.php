<?php

class Mage_Auth_Model_Mysql4_Acl extends Mage_Auth_Model_Mysql4
{
    const ROLE_TYPE_GROUP = 'G';
    const ROLE_TYPE_USER = 'U';
    
    const RULE_PERM_DENY = 0;
    const RULE_PERM_INHERIT = 1;
    const RULE_PERM_ALLOW = 2;
    
    function loadUserAcl($userId)
    {
        $acl = new Zend_Acl();
        
        Mage_Auth_Config::loadAclResources($acl);

        $roleTable = $this->_getTableName('auth_setup', 'role');
        $rolesSelect = $this->_read->select()->from($roleTable)->order(array('tree_level'));
        $rolesArr = $this->_read->fetchAll($rolesSelect);
        $this->loadRoles($acl, $rolesArr);
        
        $ruleTable = $this->_getTableName('auth_setup', 'rule');
        $assertTable = $this->_getTableName('auth_setup', 'assert');
        $rulesSelect = $this->_read->select()->from($ruleTable)
            ->joinLeft($assertTable, "$assertTable.assert_id=$ruleTable.assert_id", array('assert_type', 'assert_data'));
        $rulesArr = $this->_read->fetchAll($rulesSelect);        
        $this->loadRules($acl, $rulesArr);
        
        return $acl;
    }
    
    function loadRoles(Zend_Acl $acl, array $rolesArr)
    {
        foreach ($rolesArr as $role) {
            $parent = $role['parent_id']>0 ? self::ROLE_TYPE_GROUP.$role['parent_id'] : null;
            
            switch ($role['role_type']) {
                case self::ROLE_TYPE_GROUP:
                    $roleId = $role['role_type'].$role['role_id'];
                    $acl->addRole(new Mage_Auth_Acl_Role_Group($roleId), $parent);
                    break;
                    
                case self::ROLE_TYPE_USER:
                    $roleId = $role['role_type'].$role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(new Mage_Auth_Acl_Role_User($roleId), $parent);
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }
                    break;
            }
        }
    }
    
    function loadRules(Zend_Acl $acl, array $rulesArr)
    {
        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'].$rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = explode(',', $rule['privileges']);

            $assert = null;
            if (0!=$rule['assert_id']) {
                $assertClass = (string)Mage_Auth_Config::getAclAssert($rule['assert_type'])->class;
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
    }

}