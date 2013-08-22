<?php
/**
 * Magento_Webhook_Block_AdminHtml_Subscription_Edit
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
class Magento_Webhook_Block_Adminhtml_Subscription_EditTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Registry */
    private $_registry;

    public function setUp()
    {
        $this->_registry = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Registry');
    }

    public function tearDown()
    {
        $this->_registry->unregister('current_subscription');
    }

    public function testAddSubscriptionTitle()
    {
        /** @var Magento_Core_Model_Layout $layout */
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Layout');

        $subscription = array(
            'subscription_id' => null,
        );
        $this->_registry->register('current_subscription', $subscription);

        /** @var Magento_Webhook_Block_Adminhtml_Subscription_Edit $block */
        $block = $layout->createBlock('Magento_Webhook_Block_Adminhtml_Subscription_Edit',
            '', array('registry' => $this->_registry)
        );
        $block->toHtml();
        $this->assertEquals('Add Subscription', $block->getHeaderText());

    }

    public function testEditSubscriptionTitle()
    {
        /** @var Magento_Core_Model_Layout $layout */
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Layout');

        $subscription = array(
            'subscription_id' => 1,
        );
        $this->_registry->register('current_subscription', $subscription);

        /** @var Magento_Webhook_Block_Adminhtml_Subscription_Edit $block */
        $block = $layout->createBlock('Magento_Webhook_Block_Adminhtml_Subscription_Edit',
            '', array('registry' => $this->_registry)
        );
        $block->toHtml();
        $this->assertEquals('Edit Subscription', $block->getHeaderText());
    }
}
