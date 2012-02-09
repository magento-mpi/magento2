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
 * API2 Global ACL attribute resources permissions model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Attribute_ResourcePermission
    implements Mage_Api2_Model_Acl_Global_AclPermissionInterface
{
    /**
     * Resources permissions
     *
     * @var array
     */
    protected $_resourcesPermissions;

    /**
     * Filter item value
     *
     * @var string
     */
    protected $_userType;

    /**
     * Get resources permissions for selected role
     *
     * @return array
     */
    public function getResourcesPermissions()
    {
        if (null === $this->_resourcesPermissions) {
            $rulesPairs = array();

            if ($this->_userType) {
                /** @var $rules Mage_Api2_Model_Resource_Acl_Global_Attribute_Collection */
                $rules = Mage::getResourceModel('api2/acl_global_attribute_collection');
                $rules->addFilterByUserType($this->_userType);

                /** @var $rule Mage_Api2_Model_Acl_Global_Attribute */
                foreach ($rules as $rule) {
                    $resourceId = $rule->getResourceId();
                    $arrAttributes = explode(',', $rule->getAllowedAttributes());
                    foreach ($arrAttributes as $attribute) {
                        $rulesPairs[$resourceId]['operations'][$rule->getOperation()]['attributes'][$attribute] =
                                Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW;
                    }

                }
            } else {
                //make resource "all" as default for new item
                $rulesPairs = array(
                    Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL =>
                    Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW
                );
            }

            //set permissions to resources
            /** @var $config Mage_Api2_Model_Config */
            $config = Mage::getModel('api2/config');
            $resources = $config->getResourceGroups();
            /** @var $operationSource Mage_Api2_Model_Acl_Global_Attribute_Operation */
            $operationSource = Mage::getModel('api2/acl_global_attribute_operation');
            $operations = array_keys($operationSource->toArray());

            /** @var $node Varien_Simplexml_Element */
            foreach ($resources as $node) {
                $resourceId = (string) $node->type;
                foreach ($operations as $operation) {
                    if (!isset($rulesPairs[$resourceId]['operations'][$operation])) {
                        $rulesPairs[$resourceId]['operations'][$operation] =
                            Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_DENY;
                    }
                }
            }
            $this->_resourcesPermissions = $rulesPairs;
        }
        return $this->_resourcesPermissions;
    }

    /**
     * Set filter value
     *
     * Set user type
     *
     * @param string $userType
     * @return Mage_Api2_Model_Acl_Global_Attribute_ResourcePermission
     */
    public function setFilterValue($userType)
    {
        /** @var $userTypes Mage_Api2_Model_Auth_User_Type */
        $userTypes = Mage::getModel('api2/auth_user_type');
        if (!in_array($userType, $userTypes->toArray())) {
            throw new Exception('Unknown user type.');
        }
        $this->_userType = $userType;
        return $this;
    }
}
