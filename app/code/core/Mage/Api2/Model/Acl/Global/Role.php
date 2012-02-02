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
 * API2 Global ACL Role model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Model_Resource_Acl_Global_Role_Collection getCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Role_Collection getResourceCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Role getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Role _getResource()
 * @method string getCreatedAt()
 * @method Mage_Api2_Model_Acl_Global_Role setCreatedAt() setCreatedAt(string $createdAt)
 * @method string getUpdatedAt()
 * @method Mage_Api2_Model_Acl_Global_Role setUpdatedAt() setUpdatedAt(string $updatedAt)
 * @method string getRoleName()
 * @method Mage_Api2_Model_Acl_Global_Role setRoleName() setRoleName(string $roleName)
 */
class Mage_Api2_Model_Acl_Global_Role extends Mage_Core_Model_Abstract
{
    /**
     * Resources permissions
     *
     * @var array
     */
    protected $_resourcesPermissions;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('api2/acl_global_role');
    }

    /**
     * Before save actions
     *
     * @return Mage_OAuth_Model_Consumer
     */
    protected function _beforeSave()
    {
        if ($this->isObjectNew() && null === $this->getCreatedAt()) {
            $this->setCreatedAt(Varien_Date::now());
        } else {
            $this->setUpdatedAt(Varien_Date::now());
        }
        parent::_beforeSave();
        return $this;
    }

    /**
     * Get pairs resources-permissions for current role
     *
     * @return array
     */
    public function getResourcesPermissionsPairs()
    {
        if (null === $this->_resourcesPermissions) {
            $id = $this->getId();
            $rulesPairs = array();
            if ($id) {
                /** @var $rules Mage_Api2_Model_Resource_Acl_Global_Rule_Collection */
                $rules = Mage::getResourceModel('api2/acl_global_rule_collection');
                $rules->addFilterByRoleId($id);
                /** @var $rule Mage_Api2_Model_Acl_Global_Rule */
                foreach ($rules as $rule) {
                    $rulesPairs[$rule->getResourceId()] = $rule->getPermission();
                }
            }

            if (!$rulesPairs) {
                $rulesPairs = array(
                    Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL =>
                        Mage_Api2_Model_Acl_Global_Rule_Permission::SOURCE_ALLOW);
            }

            /** @var $config Mage_Api2_Model_Config */
            $config = Mage::getModel('api2/config');

            //set permissions to resources
            $resources = $config->getResources();
            /** @var $node Varien_Simplexml_Element */
            foreach ($resources as $node) {
                $name = (string) $node->type;
                if (!isset($rulesPairs[$name])) {
                    $rulesPairs[$name] = Mage_Api2_Model_Acl_Global_Rule_Permission::SOURCE_DISALLOW;
                }
            }

            $this->_resourcesPermissions = $rulesPairs;
        }
        return $this->_resourcesPermissions;
    }

    /**
     * Get resources
     *
     * @param bool $grouped
     * @return bool|Mage_Core_Model_Config_Element|Varien_Simplexml_Element
     */
    public function getResources($grouped = false)
    {
        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getModel('api2/config');
        $resources = $grouped ? $config->getResourceGroups() : $config->getResources();
        return $resources;
    }
}
