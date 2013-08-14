<?php
/**
 * Magento_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action
 *
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
class Magento_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_ActionTest extends PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $objectManager = Mage::getObjectManager();
        $grid = $objectManager->create('Magento_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action');

        /** @var Magento_Webhook_Model_Subscription $subscriptionRow */
        $subscriptionRow = $objectManager->create('Magento_Webhook_Model_Subscription');

        $subscriptionRow->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Revoke') !== false);

        $subscriptionRow->setStatus(Magento_Webhook_Model_Subscription::STATUS_INACTIVE);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Activate') !== false);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'activateSubscription') !== false);

        $subscriptionRow->setStatus(Magento_Webhook_Model_Subscription::STATUS_REVOKED);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Activate') !== false);

        $invalidStatus = -1;
        $subscriptionRow->setStatus($invalidStatus);
        $this->assertEquals('', $grid->render($subscriptionRow));
    }
}
