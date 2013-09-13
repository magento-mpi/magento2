<?php
/**
 * Magento_Webhook_Block_Adminhtml_Subscription
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_SubscriptionTest extends Magento_Test_Block_Adminhtml
{
    public function testConstruct()
    {
        $block = new Magento_Webhook_Block_Adminhtml_Subscription(
            $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false),
            $this->_context
        );
        $this->assertEquals('Subscriptions', $block->getHeaderText());
        $this->assertEquals('Add Subscription', $block->getAddButtonLabel());
    }
}
