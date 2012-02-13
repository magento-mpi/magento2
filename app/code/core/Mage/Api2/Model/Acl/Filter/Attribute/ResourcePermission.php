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
 * API2 filter ACL attribute resources permissions model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission
    implements Mage_Api2_Model_Acl_PermissionInterface
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
                $allowedAttributes = array();

                /** @var $rules Mage_Api2_Model_Resource_Acl_Filter_Attribute_Collection */
                $rules = Mage::getResourceModel('api2/acl_filter_attribute_collection');
                $rules->addFilterByUserType($this->_userType);

                foreach ($rules as $rule) {
                    if (Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL === $rule->getResourceId()) {
                        $rulesPairs[$rule->getResourceId()] = Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW;
                    }

                    /** @var $rule Mage_Api2_Model_Acl_Filter_Attribute */
                    if (null !== $rule->getAllowedAttributes()) {
                        $allowedAttributes[$rule->getResourceId()][$rule->getOperation()]
                            = explode(',', $rule->getAllowedAttributes());
                    }
                }

                /** @var $config Mage_Api2_Model_Config */
                $config = Mage::getModel('api2/config');

                foreach ($config->getResourcesTypes() as $resource) {
                    try {
                        /** @var $resourceModel Mage_Api2_Model_Resource_Instance */
                        $resourceModel = Mage::getModel($config->getResourceModel($resource));
                        $resourceModel->setResourceType($resource);

                        /** @var $operationSource Mage_Api2_Model_Acl_Filter_Attribute_Operation */
                        $operationSource = Mage::getModel('api2/acl_filter_attribute_operation');

                        foreach ($operationSource->toArray() as $operationValue => $operationLabel) {
                            foreach ($resourceModel->getAvailableAttributes() as $attributeValue => $attributeLabel) {
                                $status = isset($allowedAttributes[$resource][$operationValue])
                                    && in_array($attributeValue, $allowedAttributes[$resource][$operationValue])
                                        ? Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW
                                        : Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_DENY;

                                $rulesPairs[$resource]['operations'][$operationValue]['attributes'][$attributeValue]
                                    = $status;
                            }
                        }
                    } catch (Exception $e) {
                        Mage::logException($e);
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
     * @return Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission
     */
    public function setFilterValue($userType)
    {
        if (!array_key_exists($userType, Mage_Api2_Model_Auth_User::getUserTypes())) {
            throw new Exception('Unknown user type.');
        }
        $this->_userType = $userType;
        return $this;
    }
}
