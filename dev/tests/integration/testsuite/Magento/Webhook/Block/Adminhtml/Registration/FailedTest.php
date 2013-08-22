<?php
/**
 * Magento_Webhook_Block_Adminhtml_Registration_Failed
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
class Magento_Webhook_Block_Adminhtml_Registration_FailedTest extends PHPUnit_Framework_TestCase
{
    public function testGetSessionError()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();

        /** @var Magento_Backend_Model_Session $session */
        $session = $objectManager->create('Magento_Backend_Model_Session');
        $context = $objectManager->create('Magento_Core_Block_Template_Context');
        $messageCollection = $objectManager->create('Magento_Core_Model_Message_Collection');
        $message = $objectManager->create('Magento_Core_Model_Message_Notice', array('code' => ''));
        $messageCollection->addMessage($message);
        $session->setData('messages', $messageCollection);

        $block = $objectManager->create('Magento_Webhook_Block_Adminhtml_Registration_Failed',
            array($session, $context));

        $this->assertEquals($message->toString(), $block->getSessionError());
    }
}
