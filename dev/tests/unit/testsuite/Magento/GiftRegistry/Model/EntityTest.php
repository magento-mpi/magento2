<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemMock;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $resource = $this->getMock('Magento\GiftRegistry\Model\Resource\Entity', [], [], '', false);

        $this->_store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->_storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getStore'])
            ->getMockForAbstractClass();
        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_store));

        $this->_transportBuilderMock = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder',
            [],
            [],
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

        $appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);

        $eventDispatcher = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            [],
            [],
            '',
            false,
            false
        );
        $cacheManager = $this->getMock('Magento\Framework\App\CacheInterface', [], [], '', false, false);
        $logger = $this->getMock('Magento\Framework\Logger', [], [], '', false);
        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction',
            [],
            [],
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
            ['getRegistryLink'],
            [],
            '',
            false,
            false
        );
        $giftRegistryData->expects($this->any())->method('getRegistryLink')->will($this->returnArgument(0));
        $coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $attributeConfig = $this->getMock('Magento\GiftRegistry\Model\Attribute\Config', [], [], '', false);
        $this->itemModelMock = $this->getMock('Magento\GiftRegistry\Model\Item', [], [], '', false);
        $type = $this->getMock('Magento\GiftRegistry\Model\Type', [], [], '', false);
        $this->stockRegistryMock = $this->getMock(
            'Magento\CatalogInventory\Model\StockRegistry',
            [],
            [],
            '',
            false
        );
        $this->stockItemMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\StockItemRepository',
            ['getIsQtyDecimal'],
            [],
            '',
            false
        );
        $session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);

        $quoteRepository = $this->getMock('Magento\Sales\Model\QuoteRepository', [], [], '', false);
        $customerFactory = $this->getMock('Magento\Customer\Model\CustomerFactory', [], [], '', false);
        $personFactory = $this->getMock('Magento\GiftRegistry\Model\PersonFactory', [], [], '', false);
        $this->itemFactoryMock = $this->getMock('Magento\GiftRegistry\Model\ItemFactory', ['create'], [], '', false);
        $addressFactory = $this->getMock('Magento\Customer\Model\AddressFactory', [], [], '', false);
        $productRepository = $this->getMock('Magento\Catalog\Model\ProductRepository', [], [], '', false);
        $dateFactory = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTimeFactory', [], [], '', false);
        $escaper = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false, false);
        $escaper->expects($this->any())->method('escapeHtml')->will($this->returnArgument(0));
        $mathRandom = $this->getMock('Magento\Framework\Math\Random', [], [], '', false, false);
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $inlineTranslate = $this->getMock(
            '\Magento\Framework\Translate\Inline\StateInterface',
            [],
            [],
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
            $this->itemModelMock,
            $this->stockRegistryMock,
            $session,
            $quoteRepository,
            $customerFactory,
            $personFactory,
            $this->itemFactoryMock,
            $addressFactory,
            $productRepository,
            $dateFactory,
            $escaper,
            $mathRandom,
            $scopeConfig,
            $inlineTranslate,
            $resource,
            null,
            []
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
        return [
            [
                [
                    'sender_name' => null,
                    'sender_message' => 'Hello world',
                    'sender_email' => 'email',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'You need to enter sender data.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => null,
                    'sender_email' => 'email',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'You need to enter sender data.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => null,
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'You need to enter sender data.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'invalid_email',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'Please enter a valid sender email address.']
            ]
        ];
    }

    /**
     * Retrieve data for invalid recipient cases
     *
     * @return array
     */
    protected function _invalidRecipientInfoDataProvider()
    {
        return [
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => [['email' => 'invalid_email']]
                ],
                ['success' => false, 'error_message' => 'Please enter a valid recipient email address.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => [['email' => 'john.doe@example.com', 'name' => '']]
                ],
                ['success' => false, 'error_message' => 'Please enter a recipient name.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => null]
            ]
        ];
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

    public function testUpdateItems()
    {
        $modelId = 1;
        $productId = 1;
        $items = [
            1 => ['note' => 'test', 'qty' => 5],
            2 => ['note' => '', 'qty' => 1, 'delete' => 1]
        ];
        $this->_model->setId($modelId);
        $modelMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getProductId', 'getId', 'getEntityId', 'save', 'delete', 'isDeleted', 'setQty', 'setNote'],
            [],
            '',
            false
        );
        $this->itemFactoryMock->expects($this->exactly(2))->method('create')->willReturn($this->itemModelMock);
        $this->itemModelMock->expects($this->exactly(4))->method('load')->willReturn($modelMock);
        $modelMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $modelMock->expects($this->atLeastOnce())->method('getEntityId')->willReturn(1);
        $modelMock->expects($this->once())->method('getProductId')->willReturn($productId);
        $modelMock->expects($this->once())->method('delete');
        $modelMock->expects($this->once())->method('setQty')->with($items[1]['qty']);
        $modelMock->expects($this->once())->method('setNote')->with($items[1]['note']);
        $modelMock->expects($this->once())->method('save');
        $this->stockRegistryMock->expects($this->once())->method('getStockItem')->with($productId)
            ->willReturn($this->stockItemMock);
        $this->stockItemMock->expects($this->once())->method('getIsQtyDecimal')->willReturn(10);
        $this->assertEquals($this->_model, $this->_model->updateItems($items));
    }

    /**
     * @expectedException \Magento\Framework\Exception
     * @expectedExceptionMessage Please correct the  gift registry item quantity.
     */
    public function testUpdateItemsWithIncorrectQuantity()
    {
        $modelId = 1;
        $productId = 1;
        $items = [
            1 => ['note' => 'test', 'qty' => '.1']
        ];
        $this->_model->setId($modelId);
        $modelMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getProductId', 'getId', 'getEntityId'],
            [],
            '',
            false
        );
        $this->itemModelMock->expects($this->once())->method('load')->willReturn($modelMock);
        $modelMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $modelMock->expects($this->atLeastOnce())->method('getEntityId')->willReturn(1);
        $modelMock->expects($this->once())->method('getProductId')->willReturn($productId);
        $this->stockRegistryMock->expects($this->once())->method('getStockItem')->with($productId)
            ->willReturn($this->stockItemMock);
        $this->stockItemMock->expects($this->once())->method('getIsQtyDecimal')->willReturn(0);
        $this->assertEquals($this->_model, $this->_model->updateItems($items));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Please correct the gift registry item ID.
     */
    public function testUpdateItemsWithIncorrectItemId()
    {
        $modelId = 1;
        $items = [
            1 => ['note' => 'test', 'qty' => '.1']
        ];
        $this->_model->setId($modelId);
        $modelMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            [],
            [],
            '',
            false
        );
        $this->itemModelMock->expects($this->once())->method('load')->willReturn($modelMock);
        $this->assertEquals($this->_model, $this->_model->updateItems($items));
    }
}
