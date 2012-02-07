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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API User ACL model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl extends Zend_Acl
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_setResources();
        $this->_setRoles();
        $this->_setRules();
    }

    /**
     * Retrieve resources types and set into ACL
     *
     * @return Mage_Api2_Model_Acl
     */
    protected function _setResources()
    {
        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getModel('api2/config');

        foreach ($config->getResourcesTypes() as $type) {
            $this->addResource($type);
        }
        return $this;
    }

    /**
     * Retrieve roles from DB and set into ACL
     *
     * @return Mage_Api2_Model_Acl
     */
    protected function _setRoles()
    {
        /** @var $rolesCollection Mage_Api2_Model_Resource_Acl_Global_Role_Collection */
        $rolesCollection = Mage::getResourceModel('api2/acl_global_role_collection');

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        foreach ($rolesCollection as $role) {
            $this->addRole($role->getId());
        }
        return $this;
    }

    /**
     * Retrieve rules data from DB and inject it into ACL
     *
     * @return Mage_Api2_Model_Acl
     */
    protected function _setRules()
    {
        /** @var $rulesCollection Mage_Api2_Model_Resource_Acl_Global_Rule_Collection */
        $rulesCollection = Mage::getResourceModel('api2/acl_global_rule_collection');

        /** @var $rule Mage_Api2_Model_Acl_Global_Rule */
        foreach ($rulesCollection as $rule) {
            if (Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL === $rule->getResourceId()) {
                $this->allow($rule->getRoleId());
            } else {
                $this->allow($rule->getRoleId(), $rule->getResourceId(), $rule->getPrivilege());
            }
        }
        return $this;
    }

    /**
     * Adds a Role having an identifier unique to the registry
     * OVERRIDE to allow numeric roles identifiers
     *
     * @param int $roleId Role identifier
     * @param Zend_Acl_Role_Interface|string|array $parents
     * @return Zend_Acl Provides a fluent interface
     */
    public function addRole($roleId, $parents = null)
    {
        if (!is_numeric($roleId)) {
            throw new Exception('Invalid role identifier');
        }
        return parent::addRole((string) $roleId);
    }
}
