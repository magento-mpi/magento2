<?php
/**
 * Magento_Webhook_Block_Adminhtml_Registration_ActivateTest
 *
 * @magentoDbIsolation enabled
 * @magentoAppArea adminhtml
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Registration_ActivateTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        // Data for the block object
        $topics = array('array', 'of', 'topics');
        $subscriptionId = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $subscriptionData = array(
            Magento_Webhook_Block_Adminhtml_Registration_Activate::DATA_SUBSCRIPTION_ID => $subscriptionId,
            Magento_Webhook_Block_Adminhtml_Registration_Activate::DATA_NAME => 'name',
            Magento_Webhook_Block_Adminhtml_Registration_Activate::DATA_TOPICS => $topics
        );

        /** @var Magento_Core_Model_Registry $registry */
        $registry = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry');
        $registry->register(Magento_Webhook_Block_Adminhtml_Registration_Activate::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        /** @var Magento_Core_Block_Template_Context $context */
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Block_Template_Context');

        /** @var Magento_Webhook_Block_Adminhtml_Registration_Activate $block */
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Block_Adminhtml_Registration_Activate', array($context, $registry));

        $urlBuilder = $context->getUrlBuilder();
        $expectedUrl = $urlBuilder->getUrl('*/*/accept', array('id' => $subscriptionId));

        $this->assertEquals($expectedUrl, $block->getAcceptUrl());
        $this->assertEquals('name', $block->getSubscriptionName());
        $this->assertEquals($topics, $block->getSubscriptionTopics());
    }
}
