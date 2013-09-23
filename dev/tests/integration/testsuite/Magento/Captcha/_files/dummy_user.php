<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create dummy user
 */
/** @var $user Magento_User_Model_User */
$user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_User');
$user->setFirstname('Dummy')
    ->setLastname('Dummy')
    ->setEmail('dummy@dummy.com')
    ->setUsername('dummy_username')
    ->setPassword('dummy_password1')
    ->save();
