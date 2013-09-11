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
 * Test class for \Magento\GiftRegistry\Model\Entity
 */
class Magento_GiftRegistry_Model_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry instance
     *
     * @var \Magento\GiftRegistry\Model\Entity
     */
    protected $_model;

    /**
     * Mock for store instance
     *
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * Mock from email template instance
     *
     * @var \Magento\Core\Model\Email\Template
     */
    protected $_emailTemplate;

    public function setUp()
    {
        $app = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $resource = $this->getMock('Magento\GiftRegistry\Model\Resource\Entity', array(), array(), '', false);
        $helper = $this->getMock('Magento\GiftRegistry\Helper\Data',
            array('getRegistryLink'), array(), '', false, false
        );
        $translate = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);

        $factory = $this->getMock('Magento_Core_Model_Email_TemplateFactory', array('create'), array(), '', false);
        $this->_store = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $this->_emailTemplate = $this->getMock('Magento\Core\Model\Email\Template',
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

        $eventDispatcher = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false, false);
        $cacheManager = $this->getMock('Magento\Core\Model\CacheInterface', array(), array(), '', false, false);
        $context = new \Magento\Core\Model\Context($eventDispatcher, $cacheManager);

        $this->_model = new \Magento\GiftRegistry\Model\Entity(
            $context, $app, $this->_store, $translate, $factory, $resource, null, array(
                'helpers' => array('Magento\GiftRegistry\Helper\Data' => $helper)
            )
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
