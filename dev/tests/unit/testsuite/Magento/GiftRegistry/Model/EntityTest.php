<?php
/**
 * {license_notice}
 *
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_store;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_transportBuilderMock;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $resource = $this->getMock('Magento\GiftRegistry\Model\Resource\Entity', array(), array(), '', false);

        $this->_store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMockBuilder('Magento\Framework\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMockForAbstractClass();
        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_store));

        $this->_transportBuilderMock = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder',
            array(),
            array(),
            '',
            false
        );

        $this->_transportBuilderMock->expects($this->any())->method('setTemplateOptions')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('setTemplateVars')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('addTo')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('setFrom')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('setTemplateIdentifier')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('getTransport')
            ->will($this->returnValue($this->getMock('Magento\Framework\Mail\TransportInterface')));

        $this->_store->expects($this->any())->method('getId')->will($this->returnValue(1));

        $appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);

        $eventDispatcher = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            array(),
            array(),
            '',
            false,
            false
        );
        $cacheManager = $this->getMock('Magento\Framework\App\CacheInterface', array(), array(), '', false, false);
        $logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction',
            array(),
            array(),
            '',
            false
        );
        $context = new \Magento\Framework\Model\Context(
            $logger,
            $eventDispatcher,
            $cacheManager,
            $appState,
            $actionValidatorMock
        );
        $giftRegistryData = $this->getMock(
            'Magento\GiftRegistry\Helper\Data',
            array('getRegistryLink'),
            array(),
            '',
            false,
            false
        );
        $giftRegistryData->expects($this->any())->method('getRegistryLink')->will($this->returnArgument(0));
        $coreRegistry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);

        $attributeConfig = $this->getMock('Magento\GiftRegistry\Model\Attribute\Config', array(), array(), '', false);
        $item = $this->getMock('Magento\GiftRegistry\Model\Item', array(), array(), '', false);
        $type = $this->getMock('Magento\GiftRegistry\Model\Type', array(), array(), '', false);
        $stockItemService = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            array(),
            array(),
            '',
            false
        );
        $session = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);

        $quoteFactory = $this->getMock('Magento\Sales\Model\QuoteFactory', array(), array(), '', false);
        $customerFactory = $this->getMock('Magento\Customer\Model\CustomerFactory', array(), array(), '', false);
        $personFactory = $this->getMock('Magento\GiftRegistry\Model\PersonFactory', array(), array(), '', false);
        $itemFactory = $this->getMock('Magento\GiftRegistry\Model\ItemFactory', array(), array(), '', false);
        $addressFactory = $this->getMock('Magento\Customer\Model\AddressFactory', array(), array(), '', false);
        $productRepository = $this->getMock('Magento\Catalog\Model\ProductRepository', array(), array(), '', false);
        $dateFactory = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTimeFactory', array(), array(), '', false);
        $escaper = $this->getMock('Magento\Framework\Escaper', array('escapeHtml'), array(), '', false, false);
        $escaper->expects($this->any())->method('escapeHtml')->will($this->returnArgument(0));
        $mathRandom = $this->getMock('Magento\Framework\Math\Random', array(), array(), '', false, false);
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $inlineTranslate = $this->getMock(
            '\Magento\Framework\Translate\Inline\StateInterface',
            array(),
            array(),
            '',
            false,
            false
        );

        $this->_model = new \Magento\GiftRegistry\Model\Entity(
            $context,
            $coreRegistry,
            $giftRegistryData,
            $this->_storeManagerMock,
            $this->_transportBuilderMock,
            $type,
            $attributeConfig,
            $item,
            $stockItemService,
            $session,
            $quoteFactory,
            $customerFactory,
            $personFactory,
            $itemFactory,
            $addressFactory,
            $productRepository,
            $dateFactory,
            $escaper,
            $mathRandom,
            $scopeConfig,
            $inlineTranslate,
            $resource,
            null,
            array()
        );
    }

    /**
     * @param array $arguments
     * @param array $expectedResult
     * @dataProvider invalidSenderAndRecipientInfoDataProvider
     */
    public function testSendShareRegistryEmailsWithInvalidSenderAndRecipientInfoReturnsError(
        $arguments,
        $expectedResult
    ) {
        $this->_initSenderInfo($arguments['sender_name'], $arguments['sender_message'], $arguments['sender_email']);
        $this->_model->setRecipients($arguments['recipients']);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertEquals($expectedResult['success'], $result->getIsSuccess());
        $this->assertEquals($expectedResult['error_message'], $result->getErrorMessage());
    }

    public function invalidSenderAndRecipientInfoDataProvider()
    {
        return array_merge($this->_invalidRecipientInfoDataProvider(), $this->_invalidSenderInfoDataProvider());
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
                array('success' => false, 'error_message' => 'You need to enter sender data.')
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => null,
                    'sender_email' => 'email',
                    'recipients' => array()
                ),
                array('success' => false, 'error_message' => 'You need to enter sender data.')
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => null,
                    'recipients' => array()
                ),
                array('success' => false, 'error_message' => 'You need to enter sender data.')
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'invalid_email',
                    'recipients' => array()
                ),
                array('success' => false, 'error_message' => 'Please enter a valid sender email address.')
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
                    'recipients' => array(array('email' => 'invalid_email'))
                ),
                array('success' => false, 'error_message' => 'Please enter a valid recipient email address.')
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array(array('email' => 'john.doe@example.com', 'name' => ''))
                ),
                array('success' => false, 'error_message' => 'Please enter a recipient name.')
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array()
                ),
                array('success' => false, 'error_message' => null)
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
        $this->_model->setSenderName($senderName)->setSenderMessage($senderMessage)->setSenderEmail($senderEmail);
    }
}
