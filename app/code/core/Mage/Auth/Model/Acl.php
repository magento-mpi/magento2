<?php

/**
 * Acl model
 * 
 * @package     Mage
 * @subpackage  Auth
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Auth_Model_Acl extends Zend_Acl
{
    /**
     * Get role registry object or create one
     *
     * @return Mage_Auth_Model_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = Mage::getModel('auth', 'acl_role_registry');
        }
        return $this->_roleRegistry;
    }
    
    /**
     * Add parent to role object
     *
     * @param Zend_Acl_Role $role
     * @param Zend_Acl_Role $parent
     * @return Mage_Auth_Model_Acl
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}