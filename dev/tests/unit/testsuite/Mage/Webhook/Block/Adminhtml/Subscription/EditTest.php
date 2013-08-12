<?php
/**
 * Mage_Webhook_Block_Adminhtml_Subscription_Edit
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_EditTest extends Magento_Test_Block_Adminhtml
{
    /** @var  Mage_Core_Model_Registry */
    private $_registry;

    /** @var  Mage_Webhook_Block_Adminhtml_Subscription_Edit */
    private $_block;

    public function testGetHeaderTestExisting()
    {
        $subscriptionData = array(
            Mage_Webhook_Block_Adminhtml_Subscription_Edit::DATA_SUBSCRIPTION_ID => true,
            'alias' => 'alias_value');
        $this->_registry = new Mage_Core_Model_Registry();
        $this->_registry->register(Mage_Webhook_Block_Adminhtml_Subscription_Edit::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        $this->_block = new Mage_Webhook_Block_Adminhtml_Subscription_Edit(
            $this->_registry,
            $this->_context
        );
        $this->assertEquals('Edit Subscription', $this->_block->getHeaderText());

        $this->_registry->unregister(Mage_Webhook_Block_Adminhtml_Subscription_Edit::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
    }

    public function testGetHeaderTestNew()
    {
        $this->_registry = new Mage_Core_Model_Registry();
        $this->_block = new Mage_Webhook_Block_Adminhtml_Subscription_Edit(
            $this->_registry,
            $this->_context
        );

        $this->assertEquals('Add Subscription', $this->_block->getHeaderText());
    }
}