<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer\Action
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
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $grid = $objectManager->create('Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer\Action');

        /** @var \Magento\Webhook\Model\Subscription $subscriptionRow */
        $subscriptionRow = $objectManager->create('Magento\Webhook\Model\Subscription');

        $subscriptionRow->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Revoke') !== false);

        $subscriptionRow->setStatus(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Activate') !== false);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'activateSubscription') !== false);

        $subscriptionRow->setStatus(\Magento\Webhook\Model\Subscription::STATUS_REVOKED);
        $this->assertTrue(strpos($grid->render($subscriptionRow), 'Activate') !== false);

        $invalidStatus = -1;
        $subscriptionRow->setStatus($invalidStatus);
        $this->assertEquals('', $grid->render($subscriptionRow));
    }
}
