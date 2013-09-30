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

require __DIR__ . '/template.php';
require __DIR__ . '/subscribers.php';

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
/** @var $template Magento_Newsletter_Model_Template */
$template = $objectManager->create('Magento_Newsletter_Model_Template');
$template->load('fixture_tpl', 'template_code');
$templateId = $template->getId();

$currentStore = $objectManager->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getId();
$otherStore = $objectManager->get('Magento_Core_Model_StoreManagerInterface')->getStore('fixturestore')->getId();

/** @var $queue Magento_Newsletter_Model_Queue */
$queue = $objectManager->create('Magento_Newsletter_Model_Queue');
$queue->setTemplateId($templateId)
    ->setNewsletterText('{{view url="images/logo.gif"}}')
    ->setNewsletterSubject('Subject')
    ->setNewsletterSenderName('CustomerSupport')
    ->setNewsletterSenderEmail('support@example.com')
    ->setQueueStatus(Magento_Newsletter_Model_Queue::STATUS_NEVER)
    ->setQueueStartAtByString(0)
    ->setStores(array($currentStore, $otherStore))
    ->save()
;
