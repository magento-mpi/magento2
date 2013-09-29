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

$template = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\Template');
$template->load('fixture_tpl', 'template_code');
$templateId = $template->getId();

$currentStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
$otherStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\StoreManagerInterface')->getStore('fixturestore')->getId();

$queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\Queue');
$queue->setTemplateId($templateId)
    ->setNewsletterText('{{view url="images/logo.gif"}}')
    ->setNewsletterSubject('Subject')
    ->setNewsletterSenderName('CustomerSupport')
    ->setNewsletterSenderEmail('support@example.com')
    ->setQueueStatus(\Magento\Newsletter\Model\Queue::STATUS_NEVER)
    ->setQueueStartAtByString(0)
    ->setStores(array($currentStore, $otherStore))
    ->save()
;
