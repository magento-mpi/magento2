<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create dummy user
 */
$user = \Mage::getModel('Magento\User\Model\User');
$user->setFirstname('Dummy')
    ->setLastname('Dummy')
    ->setEmail('dummy@dummy.com')
    ->setUsername('dummy_username')
    ->setPassword('dummy_password1')
    ->save();
