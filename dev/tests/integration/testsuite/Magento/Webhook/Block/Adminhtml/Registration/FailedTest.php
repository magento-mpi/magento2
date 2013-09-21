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
        $context = $objectManager->create('Magento\Core\Block\Template\Context');
        $messageCollection = $objectManager->create('Magento\Core\Model\Message\Collection');
        $message = $objectManager->create('Magento\Core\Model\Message\Notice', array('code' => ''));
        $messageCollection->addMessage($message);
        $session->setData('messages', $messageCollection);

        $block = $objectManager->create('Magento\Webhook\Block\Adminhtml\Registration\Failed',
            array($session, $context));

        $this->assertEquals($message->toString(), $block->getSessionError());
    }
}
