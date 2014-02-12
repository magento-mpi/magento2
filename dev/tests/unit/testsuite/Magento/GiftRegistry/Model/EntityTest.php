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
namespace Magento\GiftRegistry\Model;

class EntityTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManagerMock;

    /**
     * Mock from email template instance
     *
     * @var \Magento\Email\Model\Template
     */
    protected $_emailTemplate;

    protected function setUp()
    {
        $resource = $this->getMock('Magento\GiftRegistry\Model\Resource\Entity', array(), array(), '', false);
        $translate = $this->getMock('Magento\TranslateInterface', array(), array(), '', false);

        $factory = $this->getMock('Magento\Email\Model\TemplateFactory', array('create'), array(), '', false);
        $this->_store = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMockForAbstractClass();
        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));
        $this->_emailTemplate = $this->getMock(
            'Magento\Email\Model\Template',
            array('setDesignConfig', 'sendTransactional', '__wakeup'),
            array(),
            '',
            false
        );

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

        $appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);

        $eventDispatcher = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false, false);
        $cacheManager = $this->getMock('Magento\App\CacheInterface', array(), array(), '', false, false);
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $context = new \Magento\Model\Context($logger, $eventDispatcher, $cacheManager, $appState, $storeManager);
        $giftRegistryData = $this->getMock('Magento\GiftRegistry\Helper\Data', array('getRegistryLink'),
            array(), '', false, false);
        $giftRegistryData->expects($this->any())
            ->method('getRegistryLink')
            ->will($this->returnArgument(0));
        $coreRegistry = $this->getMock('Magento\Registry', array(), array(), '', false);

        $attributeConfig = $this->getMock('Magento\GiftRegistry\Model\Attribute\Config', array(), array(), '', false);
        $item = $this->getMock('Magento\GiftRegistry\Model\Item', array(), array(), '', false);
        $type = $this->getMock('Magento\GiftRegistry\Model\Type', array(), array(), '', false);
        $inventoryStockItem = $this->getMock('Magento\CatalogInventory\Model\Stock\Item', array(), array(), '', false);
        $session = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);

        $quoteFactory = $this->getMock('Magento\Sales\Model\QuoteFactory', array(), array(), '', false);
        $customerFactory = $this->getMock('Magento\Customer\Model\CustomerFactory', array(), array(), '', false);
        $personFactory = $this->getMock('Magento\GiftRegistry\Model\PersonFactory', array(), array(), '', false);
        $itemFactory = $this->getMock('Magento\GiftRegistry\Model\ItemFactory', array(), array(), '', false);
        $addressFactory = $this->getMock('Magento\Customer\Model\AddressFactory', array(), array(), '', false);
        $productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false);
        $dateFactory = $this->getMock('Magento\Core\Model\DateFactory', array(), array(), '', false);
        $loggingEventFactory = $this->getMock(
            'Magento\Logging\Model\Event\ChangesFactory', array(), array(), '', false);
        $request = $this->getMock(
            'Magento\App\RequestInterface', array(), array(), '', false);
        $escaper = $this->getMock('Magento\Escaper', array('escapeHtml'), array(), '', false, false);
        $escaper->expects($this->any())
            ->method('escapeHtml')
            ->will($this->returnArgument(0));
        $mathRandom = $this->getMock('Magento\Math\Random', array(), array(), '', false, false);
        $this->_model = new \Magento\GiftRegistry\Model\Entity(
            $context, $coreRegistry, $giftRegistryData, $this->_storeManagerMock, $translate, $factory,
            $type, $attributeConfig, $item, $inventoryStockItem, $session,
            $quoteFactory, $customerFactory, $personFactory, $itemFactory, $addressFactory, $productFactory,
            $dateFactory, $loggingEventFactory, $request, $escaper, $mathRandom, $resource, null, array()
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
