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

$currentStore = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getId();
$otherStore = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->get('Magento_Core_Model_StoreManagerInterface')->getStore('fixturestore')->getId();

$subscriber = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Newsletter_Model_Subscriber');
$subscriber->setStoreId($currentStore)
    ->setSubscriberEmail('test1@example.com')
    ->setSubscriberStatus(Magento_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
$subscriber = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Newsletter_Model_Subscriber');
$subscriber->setStoreId($otherStore)
    ->setSubscriberEmail('test2@example.com')
    ->setSubscriberStatus(Magento_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
