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

        /** @var \Magento\Backend\Model\Session $session */
        $session = $objectManager->create('Magento\Backend\Model\Session');
        $context = $objectManager->create(
            'Magento\Backend\Block\Template\Context',
            array('backendSession' => $session)
        );
        $messageCollection = $objectManager->create('Magento\Message\Collection');
        $message = $objectManager->create('Magento\Message\Notice', array('code' => ''));
        $messageCollection->addMessage($message);
        $session->setData('messages', $messageCollection);

        $block = $objectManager->create('Magento\Webhook\Block\Adminhtml\Registration\Failed',
            array('context' => $context));

        $this->assertEquals($message->toString(), $block->getSessionError());
    }
}
