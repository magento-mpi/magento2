<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_GiftRegistry_Model_Entity
 */
class Enterprise_GiftRegistry_Model_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry instance
     *
     * @var Enterprise_GiftRegistry_Model_Entity
     */
    protected $_model;

    /**
     * Mock for store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Mock fro email template instance
     *
     * @var Mage_Core_Model_Email_Template
     */
    protected $_emailTemplate;

    public function setUp()
    {
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $resource = $this->getMock('Enterprise_GiftRegistry_Model_Resource_Entity', array(), array(), '', false);
        $helper = $this->getMock('Enterprise_GiftRegistry_Helper_Data');
        $translate = $this->getMock('Mage_Core_Model_Translate');
        $config = $this->getMock('Mage_Core_Model_Config', array('getModelInstance'), array(), '', false);
        $this->_store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $this->_emailTemplate = $this->getMock('Mage_Core_Model_Email_Template',
            array('setDesignConfig', 'sendTransactional'), array(), '', false
        );

        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $this->_store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $emailTemplate = $this->_emailTemplate;

        $config->expects($this->any())
            ->method('getModelInstance')
            ->with($this->equalTo('Mage_Core_Model_Email_Template'))
            ->will(
                $this->returnCallback(
                    function () use ($emailTemplate) {
                        return clone $emailTemplate;
                    }
                )
            );

        $this->_model = new Enterprise_GiftRegistry_Model_Entity(array(
            'app' => $app,
            'config' => $config,
            'translate' => $translate,
            'resource' => $resource,
            'helpers' => array('Enterprise_GiftRegistry_Helper_Data' => $helper),
            'store' => $this->_store
        ));
    }

    /**
     * @dataProvider invalidSenderInfoDataProvider
     */
    public function testSendShareRegistryEmailsWithEmptySenderInfoReturnsError($senderName, $senderMessage,
        $senderEmail
    ) {
        $this->_initSenderInfo($senderName, $senderMessage, $senderEmail);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertFalse($result->getIsSuccess());
        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('Sender data can\'t be empty.', $result->getErrorMessage());
    }

    public function testSendShareRegistryEmailsWithInvalidSenderEmailReturnsError()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'invalid_email');
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertFalse($result->getIsSuccess());
        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('Please input a valid sender email address.', $result->getErrorMessage());
    }

    public function testSendShareRegistryEmailsWithEmptyRecipientsDoesNothing()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array());
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertFalse($result->getIsSuccess());
        $this->assertFalse($result->hasErrorMessage());
    }

    public function testSendShareRegistryEmailsWithInvalidRecipientEmailReturnsError()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(
            array('email' => 'invalid_email')
        ));
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertFalse($result->getIsSuccess());
        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('Please input a valid recipient email address.', $result->getErrorMessage());
    }

    public function testSendShareRegistryEmailsWithEmptyRecipientNameReturnsError()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(
            array(
                'email' => 'john.doe@example.com',
                'name' => ''
            )
        ));
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertFalse($result->getIsSuccess());
        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('Please input a recipient name.', $result->getErrorMessage());
    }

    public function testSendShareRegistryEmailsWithValidDataReturnsSuccess()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(
            array(
                'email' => 'john.doe@example.com',
                'name' => 'John Doe'
            )
        ));
        $this->_emailTemplate->setSentSuccess(true);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertTrue($result->getIsSuccess());
        $this->assertTrue($result->hasSuccessMessage());
    }

    public function testSendShareRegistryEmailsWithErrorInMailerReturnsError()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(
            array(
                'email' => 'john.doe@example.com',
                'name' => 'John Doe'
            )
        ));
        $this->_emailTemplate->setSentSuccess(false);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('Failed to share gift registry.', $result->getErrorMessage());
    }

    public function invalidSenderInfoDataProvider()
    {
        return array(
            array(null, 'message', 'email'),
            array('name', null, 'email'),
            array('name', 'msg', null)
        );
    }

    protected function _initSenderInfo($senderName, $senderMessage, $senderEmail)
    {
        $this->_model
            ->setSenderName($senderName)
            ->setSenderMessage($senderMessage)
            ->setSenderEmail($senderEmail);
    }
}
