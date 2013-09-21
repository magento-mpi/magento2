<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

/**
 * \Magento\Webhook\Block\Adminhtml\Registration\ActivateTest
 *
 * @magentoDbIsolation enabled
 * @magentoAppArea adminhtml
 */
class ActivateTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        // Data for the block object
        $topics = array('array', 'of', 'topics');
        $subscriptionId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $subscriptionData = array(
            \Magento\Webhook\Block\Adminhtml\Registration\Activate::DATA_SUBSCRIPTION_ID => $subscriptionId,
            \Magento\Webhook\Block\Adminhtml\Registration\Activate::DATA_NAME => 'name',
            \Magento\Webhook\Block\Adminhtml\Registration\Activate::DATA_TOPICS => $topics
        );

        /** @var \Magento\Core\Model\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Registry');
        $registry->register(\Magento\Webhook\Block\Adminhtml\Registration\Activate::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        /** @var \Magento\Core\Block\Template\Context $context */
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Block\Template\Context');

        /** @var \Magento\Webhook\Block\Adminhtml\Registration\Activate $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Block\Adminhtml\Registration\Activate', array($context, $registry));

        $urlBuilder = $context->getUrlBuilder();
        $expectedUrl = $urlBuilder->getUrl('*/*/accept', array('id' => $subscriptionId));

        $this->assertEquals($expectedUrl, $block->getAcceptUrl());
        $this->assertEquals('name', $block->getSubscriptionName());
        $this->assertEquals($topics, $block->getSubscriptionTopics());
    }
}
