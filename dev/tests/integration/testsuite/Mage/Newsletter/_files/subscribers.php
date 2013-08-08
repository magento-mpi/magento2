<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Core/_files/store.php';

$currentStore = Mage::app()->getStore()->getId();
$otherStore = Mage::app()->getStore('fixturestore')->getId();

$subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber');
$subscriber->setStoreId($currentStore)
    ->setSubscriberEmail('test1@example.com')
    ->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
$subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber');
$subscriber->setStoreId($otherStore)
    ->setSubscriberEmail('test2@example.com')
    ->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
    ->save()
;
