<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Subscription
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
        $block = new \Magento\Webhook\Block\Adminhtml\Subscription($this->_context);
        $this->assertEquals('Subscriptions', $block->getHeaderText());
        $this->assertEquals('Add Subscription', $block->getAddButtonLabel());
    }
}
