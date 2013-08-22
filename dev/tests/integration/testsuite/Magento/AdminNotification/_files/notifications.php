<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
$om = Magento_Test_Helper_Bootstrap::getObjectManager();
$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_CRITICAL)
    ->setTitle('Unread Critical 1')
    ->save();

$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_MAJOR)
    ->setTitle('Unread Major 1')
    ->save();

$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_CRITICAL)
    ->setTitle('Unread Critical 2')
    ->save();

$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_CRITICAL)
    ->setTitle('Unread Critical 3')
    ->save();

$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_CRITICAL)
    ->setTitle('Read Critical 1')
    ->setIsRead(1)
    ->save();

$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_MAJOR)
    ->setTitle('Unread Major 2')
    ->save();

$mesasge = $om->create('Magento_AdminNotification_Model_Inbox');
$mesasge->setSeverity(Magento_AdminNotification_Model_Inbox::SEVERITY_CRITICAL)
    ->setTitle('Removed Critical 1')
    ->setIsRemove(1)
    ->save();

