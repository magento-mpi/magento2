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

if (!Magento_Test_Webservice::getFixture('admin_acl_is_prepared')) {
    // Prepare role
    /* @var $admin Mage_Admin_Model_User */
    $admin = Mage::getModel('admin/user')
        ->loadByUsername(TESTS_ADMIN_USERNAME);

    /* @var $role Mage_Api2_Model_Acl_Global_Role */
    $role = Mage::getModel('api2/acl_global_role')
        ->getCollection()
        ->addFilterByAdminId($admin->getId())
        ->getFirstItem();
    if (!$role->getId()) {
        // Create role
        /* @var $role Mage_Api2_Model_Acl_Global_Role */
        $role->setRoleName('TestAdminRole' . time())
            ->save();
        Magento_Test_Webservice::setFixture('role', $role);

        // Create admin to role relathion
        /* @var $resourceModel Mage_Api2_Model_Resource_Acl_Global_Role */
        $roleResourceModel = Mage::getResourceModel('api2/acl_global_role');
        $roleResourceModel->saveAdminToRoleRelation(
            $admin->getId(),
            $role->getId()
        );
    }

    // Prepare global acl
    /* @var $rule Mage_Api2_Model_Acl_Global_Rule */
    $rule = Mage::getModel('api2/acl_global_rule');
    $roleId = $role->getId();
    $count = $rule->getCollection()
        ->addFieldToFilter('role_id', array('eq' => $roleId))
        ->addFieldToFilter('resource_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL))
        ->count();
    if (!$count) {
        $rule->setRoleId($roleId)
            ->setResourceId(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL)
            ->save();
        Magento_Test_Webservice::setFixture('rule', $rule);
    }

    // Prepare local filters
    /* @var $attribute Mage_Api2_Model_Acl_Filter_Attribute */
    $attribute = Mage::getModel('api2/acl_filter_attribute');
    $count = $attribute->getCollection()
        ->addFieldToFilter('user_type', array(
            'eq' => Mage_Api2_Model_Auth_User_Admin::USER_TYPE))
        ->addFieldToFilter('resource_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL))
        ->count();
    if (!$count) {
        $attribute->setUserType(Mage_Api2_Model_Auth_User_Admin::USER_TYPE)
            ->setResourceId(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL)
            ->save();
        Magento_Test_Webservice::setFixture('attribute', $attribute);
    }

    Magento_Test_Webservice::setFixture('admin_acl_is_prepared', true);
}
