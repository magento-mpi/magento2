<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
    ->loadArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);
$user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_User_Model_User');
$user->setUsername('newuser')
    ->setFirstname('first_name')
    ->setLastname('last_name')
    ->setPassword('password1')
    ->setEmail('newuser@example.com')
    ->setRoleId(1)
    ->save();

$role = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_Role');
$role->setName('newrole')->save();
