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

Mage::app()->loadArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
$user = Mage::getModel('\Magento\User\Model\User');
$user->setUsername('newuser')
    ->setFirstname('first_name')
    ->setLastname('last_name')
    ->setPassword('password1')
    ->setEmail('newuser@example.com')
    ->setRoleId(1)
    ->save();

$role = Mage::getModel('\Magento\User\Model\Role');
$role->setName('newrole')->save();
