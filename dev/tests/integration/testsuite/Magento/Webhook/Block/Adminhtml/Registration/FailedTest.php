<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

/**
 * \Magento\Webhook\Block\Adminhtml\Registration\Failed
 *
 * @magentoAppArea adminhtml
 */
class FailedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSessionError()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Message\ManagerInterface $messageManager */
        $messageManager = $objectManager->create('Magento\Message\ManagerInterface');
        $context = $objectManager->create(
            'Magento\Backend\Block\Template\Context',
            array('messageManager' => $messageManager)
        );

        /** @var \Magento\Message\Notice $message */
        $message = $objectManager->create('Magento\Message\Notice', array('text' => ''));
        $messageManager->addMessage($message);

        /** @var \Magento\Webhook\Block\Adminhtml\Registration\Failed $block */
        $block = $objectManager->create(
            'Magento\Webhook\Block\Adminhtml\Registration\Failed',
            array('context' => $context)
        );

        $this->assertEquals($message->toString(), $block->getSessionError());
    }
}
