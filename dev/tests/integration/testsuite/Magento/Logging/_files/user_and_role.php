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

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
    ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
$user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\User');
$user->setUsername('newuser')
    ->setFirstname('first_name')
    ->setLastname('last_name')
    ->setPassword('password1')
    ->setEmail('newuser@example.com')
    ->setRoleId(1)
    ->save();

$role = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\User\Model\Role');
$role->setName('newrole')->save();
