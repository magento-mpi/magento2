<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Subscription\Edit
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
    /** @var  \Magento\Core\Model\Registry */
    private $_registry;

    /** @var  \Magento\Webhook\Block\Adminhtml\Subscription\Edit */
    private $_block;

    public function testGetHeaderTestExisting()
    {
        $subscriptionData = array(
            \Magento\Webhook\Block\Adminhtml\Subscription\Edit::DATA_SUBSCRIPTION_ID => true,
            'alias' => 'alias_value');
        $this->_registry = new \Magento\Core\Model\Registry();
        $this->_registry->register(
            \Magento\Webhook\Block\Adminhtml\Subscription\Edit::REGISTRY_KEY_CURRENT_SUBSCRIPTION,
            $subscriptionData);

        $this->_block = new \Magento\Webhook\Block\Adminhtml\Subscription\Edit(
            $this->_registry,
            $this->_context
        );
        $this->assertEquals('Edit Subscription', $this->_block->getHeaderText());

        $this->_registry->unregister(
            \Magento\Webhook\Block\Adminhtml\Subscription\Edit::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
    }

    public function testGetHeaderTestNew()
    {
        $this->_registry = new \Magento\Core\Model\Registry();
        $this->_block = new \Magento\Webhook\Block\Adminhtml\Subscription\Edit(
            $this->_registry,
            $this->_context
        );

        $this->assertEquals('Add Subscription', $this->_block->getHeaderText());
    }
}
