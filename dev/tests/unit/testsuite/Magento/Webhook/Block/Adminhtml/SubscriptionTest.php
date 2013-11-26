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
namespace Magento\Webhook\Block\Adminhtml;

class SubscriptionTest extends \Magento\Test\Block\Adminhtml
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    public function testConstruct()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $block = $this->_objectManagerHelper->getObject(
            '\Magento\Webhook\Block\Adminhtml\Subscription', array());


        $this->assertEquals('Subscriptions', $block->getHeaderText());
        $this->assertEquals('Add Subscription', $block->getAddButtonLabel());
    }
}
