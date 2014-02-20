<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Core\Model\Website $website */
$website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Website');
$website->setName('Second Website')
        ->setCode('secondwebsite')
        ->save();

$websiteId = $website->getId();
$groupId = $website->getDefaultGroupId();

/** @var \Magento\Core\Model\Store $store */
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Store');
$store->setCode('secondstore') // fixture_store conflicts with "current_store" notation
    ->setName('Second Store')
    ->setSortOrder(10)
    ->setIsActive(1);
$store->save();


/** @var \Magento\Core\Model\Website $website */
$website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Website');
$website->setName('Third Website')
    ->setCode('thirdwebsite')
    ->save();

$websiteId = $website->getId();
$groupId = $website->getDefaultGroupId();

/** @var \Magento\Core\Model\Store $store */
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Store');
$store->setCode('thirdstore') // fixture_store conflicts with "current_store" notation
    ->setName('Third Store')
    ->setSortOrder(10)
    ->setIsActive(1);
$store->save();

/* Refresh stores memory cache */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
    ->reinitStores();
