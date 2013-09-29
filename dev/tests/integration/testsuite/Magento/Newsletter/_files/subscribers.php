<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Core/_files/store.php';

$currentStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
$otherStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\StoreManagerInterface')->getStore('fixturestore')->getId();

$subscriber = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\Subscriber');
$subscriber->setStoreId($currentStore)
    ->setSubscriberEmail('test1@example.com')
    ->setSubscriberStatus(\Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
$subscriber = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\Subscriber');
$subscriber->setStoreId($otherStore)
    ->setSubscriberEmail('test2@example.com')
    ->setSubscriberStatus(\Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
