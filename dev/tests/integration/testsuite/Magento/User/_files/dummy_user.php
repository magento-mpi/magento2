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
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
    ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
$user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\User\Model\User');
$user->setFirstname('Dummy')
    ->setLastname('Dummy')
    ->setEmail('dummy@dummy.com')
    ->setUsername('dummy_username')
    ->setPassword('dummy_password1')
    ->save();
