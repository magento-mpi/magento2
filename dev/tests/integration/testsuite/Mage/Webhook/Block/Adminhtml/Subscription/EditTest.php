<?php
/**
 * Mage_Webhook_Block_AdminHtml_Subscription_Edit
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
class Mage_Webhook_Block_Adminhtml_Subscription_EditTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Registry */
    private $_registry;

    public function setUp()
    {
        $this->_registry = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Registry');
    }

    public function tearDown()
    {
        $this->_registry->unregister('current_subscription');
    }

    public function testAddSubscriptionTitle()
    {
        /** @var Mage_Core_Model_Layout $layout */
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Layout');

        $subscription = array(
            'subscription_id' => null,
        );
        $this->_registry->register('current_subscription', $subscription);

        /** @var Mage_Webhook_Block_Adminhtml_Subscription_Edit $block */
        $block = $layout->createBlock('Mage_Webhook_Block_Adminhtml_Subscription_Edit',
            '', array('registry' => $this->_registry)
        );
        $block->toHtml();
        $this->assertEquals('Add Subscription', $block->getHeaderText());

    }

    public function testEditSubscriptionTitle()
    {
        /** @var Mage_Core_Model_Layout $layout */
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Layout');

        $subscription = array(
            'subscription_id' => 1,
        );
        $this->_registry->register('current_subscription', $subscription);

        /** @var Mage_Webhook_Block_Adminhtml_Subscription_Edit $block */
        $block = $layout->createBlock('Mage_Webhook_Block_Adminhtml_Subscription_Edit',
            '', array('registry' => $this->_registry)
        );
        $block->toHtml();
        $this->assertEquals('Edit Subscription', $block->getHeaderText());
    }
}