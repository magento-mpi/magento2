<?php
/**
 * Mage_Webhook_Block_Adminhtml_Registration_Failed
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
class Mage_Webhook_Block_Adminhtml_Registration_FailedTest extends PHPUnit_Framework_TestCase
{
    public function testGetSessionError()
    {
        $objectManager = Mage::getObjectManager();

        /** @var Mage_Backend_Model_Session $session */
        $session = $objectManager->create('Mage_Backend_Model_Session');
        $context = $objectManager->create('Mage_Core_Block_Template_Context');
        $messageCollection = $objectManager->create('Mage_Core_Model_Message_Collection');
        $message = $objectManager->create('Mage_Core_Model_Message_Notice', array('code' => ''));
        $messageCollection->addMessage($message);
        $session->setData('messages', $messageCollection);

        $block = $objectManager->create('Mage_Webhook_Block_Adminhtml_Registration_Failed',
            array($session, $context));

        $this->assertEquals($message->toString(), $block->getSessionError());
    }
}