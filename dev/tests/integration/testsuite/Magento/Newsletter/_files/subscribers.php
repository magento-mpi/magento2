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
require __DIR__ . '/../../../Magento/Customer/_files/two_customers.php';

$currentStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
$otherStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\StoreManagerInterface')->getStore('fixturestore')->getId();

$subscriber = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\Subscriber');
$subscriber->setStoreId($currentStore)
    ->setCustomerId(1)
    ->setSubscriberEmail('customer@example.com')
    ->setSubscriberStatus(\Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
$subscriber = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\Subscriber');
$subscriber->setStoreId($otherStore)
    // Intentionally setting ID to 0 instead of 2 to test fallback mechanism in Subscriber model
    ->setCustomerId(0)
    ->setSubscriberEmail('customer_two@example.com')
    ->setSubscriberStatus(\Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
