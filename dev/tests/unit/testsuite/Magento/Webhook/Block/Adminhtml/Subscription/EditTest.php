<?php
/**
 * Magento_Webhook_Block_Adminhtml_Subscription_Edit
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Subscription_EditTest extends Magento_Test_Block_Adminhtml
{
    /** @var  Magento_Core_Model_Registry */
    private $_registry;

    /** @var  Magento_Webhook_Block_Adminhtml_Subscription_Edit */
    private $_block;

    public function testGetHeaderTestExisting()
    {
        $subscriptionData = array(
            Magento_Webhook_Block_Adminhtml_Subscription_Edit::DATA_SUBSCRIPTION_ID => true,
            'alias' => 'alias_value');
        $this->_registry = new Magento_Core_Model_Registry();
        $this->_registry->register(Magento_Webhook_Block_Adminhtml_Subscription_Edit::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        $this->_block = new Magento_Webhook_Block_Adminhtml_Subscription_Edit(
            $this->_context,
            $this->_registry
        );
        $this->assertEquals('Edit Subscription', $this->_block->getHeaderText());

        $this->_registry->unregister(
            Magento_Webhook_Block_Adminhtml_Subscription_Edit::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
    }

    public function testGetHeaderTestNew()
    {
        $this->_registry = new Magento_Core_Model_Registry();
        $this->_block = new Magento_Webhook_Block_Adminhtml_Subscription_Edit(
            $this->_context,
            $this->_registry
        );

        $this->assertEquals('Add Subscription', $this->_block->getHeaderText());
    }
}
