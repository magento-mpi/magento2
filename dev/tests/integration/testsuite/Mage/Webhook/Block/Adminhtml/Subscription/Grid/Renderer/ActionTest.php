<?php
/**
 * Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action
 *
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
class Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_ActionTest extends PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $grid = $objectManager->create('Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action');

        /** @var Mage_Webhook_Model_Subscription $subscriptionRow */
        $subscriptionRow = $objectManager->create('Mage_Webhook_Model_Subscription');

        $subscriptionRow->setStatus(Mage_Webhook_Model_Subscription::STATUS_ACTIVE);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Revoke') !== false);

        $subscriptionRow->setStatus(Mage_Webhook_Model_Subscription::STATUS_INACTIVE);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Activate') !== false);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'activateSubscription') !== false);

        $subscriptionRow->setStatus(Mage_Webhook_Model_Subscription::STATUS_REVOKED);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Activate') !== false);

        $invalidStatus = -1;
        $subscriptionRow->setStatus($invalidStatus);
        $this->assertEquals('', $grid->render($subscriptionRow));
    }
}
