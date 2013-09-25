<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$store = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Core_Model_Store');
$websiteId = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
    ->getWebsite()->getId();
$groupId = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
    ->getWebsite()->getDefaultGroupId();
$store->setCode('fixturestore') // fixture_store conflicts with "current_store" notation
    ->setWebsiteId($websiteId)
    ->setGroupId($groupId)
    ->setName('Fixture Store')
    ->setSortOrder(10)
    ->setIsActive(1)
;
$store->save();

/* Refresh stores memory cache */
Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
    ->reinitStores();
