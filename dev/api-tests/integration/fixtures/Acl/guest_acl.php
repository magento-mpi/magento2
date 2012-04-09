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
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!Magento_Test_Webservice::getFixture('guest_acl_is_prepared')) {
    // Prepare global acl
    /* @var $rule Mage_Api2_Model_Acl_Global_Rule */
    $rule = Mage::getModel('api2/acl_global_rule');
    $count = $rule->getCollection()
        ->addFieldToFilter('role_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID))
        ->addFieldToFilter('resource_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL))
        ->count();
    if (!$count) {
        $rule->setRoleId(Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID)
            ->setResourceId(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL)
            ->save();
        Magento_Test_Webservice::setFixture('rule', $rule);
    }

    // Prepare local filters
    /* @var $attribute Mage_Api2_Model_Acl_Filter_Attribute */
    $attribute = Mage::getModel('api2/acl_filter_attribute');
    $count = $attribute->getCollection()
        ->addFieldToFilter('user_type', array(
            'eq' => Mage_Api2_Model_Auth_User_Guest::USER_TYPE))
        ->addFieldToFilter('resource_id', array(
            'eq' => Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL))
        ->count();
    if (!$count) {
        $attribute->setUserType(Mage_Api2_Model_Auth_User_Guest::USER_TYPE)
            ->setResourceId(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL)
            ->save();
        Magento_Test_Webservice::setFixture('attribute', $attribute);
    }

    Magento_Test_Webservice::setFixture('guest_acl_is_prepared', true);
}
