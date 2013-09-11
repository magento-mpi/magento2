<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Subscription
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
class Magento_Webhook_Block_Adminhtml_SubscriptionTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\ObjectManager */
    private $_objectManager;

    public function testConstruct()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $block = $this->_objectManager->create('Magento\Webhook\Block\Adminhtml\Subscription');
        $this->assertEquals('Subscriptions', $block->getHeaderText());
        $this->assertEquals('Add Subscription', $block->getAddButtonLabel());
    }
}
