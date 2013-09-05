<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_GiftRegistry_Model_Entity
 */
class Magento_GiftRegistry_Model_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry instance
     *
     * @var Magento_GiftRegistry_Model_Entity
     */
    protected $_model;

    /**
     * Mock for store instance
     *
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * Mock from email template instance
     *
     * @var Magento_Core_Model_Email_Template
     */
    protected $_emailTemplate;

    public function setUp()
    {
        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $resource = $this->getMock('Magento_GiftRegistry_Model_Resource_Entity', array(), array(), '', false);
        $translate = $this->getMock('Magento_Core_Model_Translate', array(), array(), '', false);

        $factory = $this->getMock('Magento_Core_Model_Email_TemplateFactory', array('create'), array(), '', false);
        $this->_store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $this->_emailTemplate = $this->getMock('Magento_Core_Model_Email_Template',
            array('setDesignConfig', 'sendTransactional'), array(), '', false
        );

        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $this->_store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $emailTemplate = $this->_emailTemplate;

        $factory->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(
                function () use ($emailTemplate) {
                    return clone $emailTemplate;
                }
            ));

        $eventDispatcher = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false, false);
        $cacheManager = $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false, false);
        $context = new Magento_Core_Model_Context($eventDispatcher, $cacheManager);
        $coreData = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false, false);
        $giftRegistryData = $this->getMock('Magento_GiftRegistry_Helper_Data', array('escapeHtml', 'getRegistryLink'),
            array(), '', false, false);
        $giftRegistryData->expects($this->any())
            ->method('escapeHtml')
            ->will($this->returnArgument(0));
        $giftRegistryData->expects($this->any())
            ->method('getRegistryLink')
            ->will($this->returnArgument(0));

        $this->_model = new Magento_GiftRegistry_Model_Entity(
            $coreData, $giftRegistryData, $context, $app, $this->_store, $translate, $factory, $resource, null, array()
        );
    }

    /**
     * @param array $arguments
     * @param array $expectedResult
     * @dataProvider invalidSenderAndRecipientInfoDataProvider
     */
    public function testSendShareRegistryEmailsWithInvalidSenderAndRecipientInfoReturnsError($arguments,
        $expectedResult
    ) {
        $this->_initSenderInfo($arguments['sender_name'], $arguments['sender_message'], $arguments['sender_email']);
        $this->_model->setRecipients($arguments['recipients']);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertEquals($expectedResult['success'], $result->getIsSuccess());
        $this->assertEquals($expectedResult['error_message'], $result->getErrorMessage());
    }

    public function testSendShareRegistryEmailsWithValidDataReturnsSuccess()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(array(
            'email' => 'john.doe@example.com',
            'name' => 'John Doe'
        )));
        $this->_emailTemplate->setSentSuccess(true);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertTrue($result->getIsSuccess());
        $this->assertTrue($result->hasSuccessMessage());
    }

    public function testSendShareRegistryEmailsWithErrorInMailerReturnsError()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(array(
            'email' => 'john.doe@example.com',
            'name' => 'John Doe'
        )));
        $this->_emailTemplate->setSentSuccess(false);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('We couldn\'t share the registry.', (string)$result->getErrorMessage());
    }

    public function invalidSenderAndRecipientInfoDataProvider()
    {
        return array_merge(
            $this->_invalidRecipientInfoDataProvider(),
            $this->_invalidSenderInfoDataProvider()
        );
    }

    /**
     * Retrieve data for invalid sender cases
     *
     * @return array
     */
    protected function _invalidSenderInfoDataProvider()
    {
        return array(
            array(
                array(
                    'sender_name' => null,
                    'sender_message' => 'Hello world',
                    'sender_email' => 'email',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'You need to enter sender data.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => null,
                    'sender_email' => 'email',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'You need to enter sender data.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => null,
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'You need to enter sender data.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'invalid_email',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'Please enter a valid sender email address.'
                )
            )
        );
    }

    /**
     * Retrieve data for invalid recipient cases
     *
     * @return array
     */
    protected function _invalidRecipientInfoDataProvider()
    {
        return array(
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array(array(
                        'email' => 'invalid_email'
                    ))
                ),
                array(
                    'success' => false,
                    'error_message' => 'Please enter a valid recipient email address.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array(array(
                        'email' => 'john.doe@example.com',
                        'name' => ''
                    ))
                ),
                array(
                    'success' => false,
                    'error_message' => 'Please enter a recipient name.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => null
                )
            )
        );
    }

    /**
     * Initialize sender info
     *
     * @param string $senderName
     * @param string $senderMessage
     * @param string $senderEmail
     * @return void
     */
    protected function _initSenderInfo($senderName, $senderMessage, $senderEmail)
    {
        $this->_model
            ->setSenderName($senderName)
            ->setSenderMessage($senderMessage)
            ->setSenderEmail($senderEmail);
    }
}
