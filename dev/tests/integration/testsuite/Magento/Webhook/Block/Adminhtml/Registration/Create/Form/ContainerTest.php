<?php
/**
 * Magento_Webhook_Block_Adminhtml_Registration_Create_Form_Container
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
        // Data for the block object
        $subscriptionId = Mage::getObjectManager()->create('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $subscriptionData = array(
            Magento_Webhook_Block_Adminhtml_Registration_Activate::DATA_SUBSCRIPTION_ID => $subscriptionId,
            Magento_Webhook_Block_Adminhtml_Registration_Activate::DATA_NAME => 'name',
        );

        /** @var Magento_Core_Model_Registry $registry */
        $registry = Mage::getObjectManager()->get('Magento_Core_Model_Registry');
        $registry->register(Magento_Webhook_Block_Adminhtml_Registration_Activate::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        /** @var Magento_Core_Block_Template_Context $context */
        $context = Mage::getObjectManager()->create('Magento_Core_Block_Template_Context');

        /** @var Magento_Webhook_Block_Adminhtml_Registration_Activate $block */
        $block = Mage::getObjectManager()
            ->create('Magento_Webhook_Block_Adminhtml_Registration_Create_Form_Container', array(
                $context,
                $registry
        ));

        $urlBuilder = $context->getUrlBuilder();
        $expectedUrl = $urlBuilder->getUrl('*/*/register', array('id' => $subscriptionId));

        $this->assertEquals($expectedUrl, $block->getSubmitUrl());
        $this->assertEquals('name', $block->getSubscriptionName());
    }
}
