<?php
/**
 * Mage_Webhook_Block_Adminhtml_Registration_Create_Form_Container
 *
 * @magentoDbIsolation enabled
 * @magentoAppArea adminhtml
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Registration_Create_Form_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        // Data for the block object
        $subscriptionId = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $subscriptionData = array(
            Mage_Webhook_Block_Adminhtml_Registration_Activate::DATA_SUBSCRIPTION_ID => $subscriptionId,
            Mage_Webhook_Block_Adminhtml_Registration_Activate::DATA_NAME => 'name',
        );

        /** @var Magento_Core_Model_Registry $registry */
        $registry = Mage::getObjectManager()->get('Magento_Core_Model_Registry');
        $registry->register(Mage_Webhook_Block_Adminhtml_Registration_Activate::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        /** @var Magento_Core_Block_Template_Context $context */
        $context = Mage::getObjectManager()->create('Magento_Core_Block_Template_Context');

        /** @var Mage_Webhook_Block_Adminhtml_Registration_Activate $block */
        $block = Mage::getObjectManager()
            ->create('Mage_Webhook_Block_Adminhtml_Registration_Create_Form_Container', array(
                $context,
                $registry
        ));

        $urlBuilder = $context->getUrlBuilder();
        $expectedUrl = $urlBuilder->getUrl('*/*/register', array('id' => $subscriptionId));

        $this->assertEquals($expectedUrl, $block->getSubmitUrl());
        $this->assertEquals('name', $block->getSubscriptionName());
    }
}