<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Magento_Test_Webservice::getFixture('customer_acl_is_prepared')) {
    // Prepare global acl
    /* @var $rule Mage_Api2_Model_Acl_Global_Rule */
    $rule = Mage::getModel('api2/acl_global_rule');
    $count = $rule->getCollection()
        ->addFieldToFilter('role_id', array('eq' => $roleId))
        ->addFieldToFilter('resource_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL))
        ->count();
    if (!$count) {
        $rule->setRoleId(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID)
            ->setResourceId(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL)
            ->save();
        Magento_Test_Webservice::setFixture('rule', $rule);
    }

    // Prepare local filters
    /* @var $attribute Mage_Api2_Model_Acl_Filter_Attribute */
    $attribute = Mage::getModel('api2/acl_filter_attribute');
    $count = $attribute->getCollection()
        ->addFieldToFilter('user_type', array(
            'eq' => Mage_Api2_Model_Auth_User_Customer::USER_TYPE))
        ->addFieldToFilter('resource_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL))
        ->count();
    if (!$count) {
        $attribute->setUserType(Mage_Api2_Model_Auth_User_Customer::USER_TYPE)
            ->setResourceId(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL)
            ->save();
        Magento_Test_Webservice::setFixture('attribute', $attribute);
    }

    Magento_Test_Webservice::setFixture('customer_acl_is_prepared', true);
}
