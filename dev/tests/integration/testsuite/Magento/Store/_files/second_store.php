<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
$websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\StoreManagerInterface'
)->getWebsite()->getId();
$groupId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\StoreManagerInterface'
)->getWebsite()->getDefaultGroupId();
$store->setCode(
    'fixture_second_store'
)->setWebsiteId(
    $websiteId
)->setGroupId(
    $groupId
)->setName(
    'Fixture Store'
)->setSortOrder(
    10
)->setIsActive(
    1
);
$store->save();

/* Refresh stores memory cache */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\StoreManagerInterface'
)->reinitStores();
