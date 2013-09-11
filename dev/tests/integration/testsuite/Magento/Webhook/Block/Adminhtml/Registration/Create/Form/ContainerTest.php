<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Registration\Create\Form\Container
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
class Magento_Webhook_Block_Adminhtml_Registration_Create_Form_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        // Data for the block object
        $subscriptionId = $objectManager->create('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $subscriptionData = array(
            \Magento\Webhook\Block\Adminhtml\Registration\Activate::DATA_SUBSCRIPTION_ID => $subscriptionId,
            \Magento\Webhook\Block\Adminhtml\Registration\Activate::DATA_NAME => 'name',
        );

        /** @var \Magento\Core\Model\Registry $registry */
        $registry = $objectManager->get('Magento\Core\Model\Registry');
        $registry->register(\Magento\Webhook\Block\Adminhtml\Registration\Activate::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        /** @var \Magento\Core\Block\Template\Context $context */
        $context = $objectManager->create('Magento\Core\Block\Template\Context');

        /** @var \Magento\Webhook\Block\Adminhtml\Registration\Activate $block */
        $block = $objectManager
            ->create('Magento\Webhook\Block\Adminhtml\Registration\Create\Form\Container', array(
                $context,
                $registry
        ));

        $urlBuilder = $context->getUrlBuilder();
        $expectedUrl = $urlBuilder->getUrl('*/*/register', array('id' => $subscriptionId));

        $this->assertEquals($expectedUrl, $block->getSubmitUrl());
        $this->assertEquals('name', $block->getSubscriptionName());
    }
}
