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

$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Store');
$websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
    ->getWebsite()->getId();
$groupId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
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
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
    ->reinitStores();
