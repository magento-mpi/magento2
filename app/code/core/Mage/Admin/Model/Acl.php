<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Acl model
 * 
 * @category   Mage
 * @package    Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Acl extends Zend_Acl
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
     * Get role registry object or create one
     *
     * @return Mage_Admin_Model_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = Mage::getModel('admin/acl_role_registry');
        }
        return $this->_roleRegistry;
    }
    
    /**
     * Add parent to role object
     *
     * @param Zend_Acl_Role $role
     * @param Zend_Acl_Role $parent
     * @return Mage_Admin_Model_Acl
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}