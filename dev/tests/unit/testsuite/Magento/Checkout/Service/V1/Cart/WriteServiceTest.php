<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Service\V1\Cart\WriteService
     */
    protected $service;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->quoteFactoryMock = $this->getMock(
            '\Magento\Sales\Model\QuoteFactory', ['create', '__wakeup'], [], '', false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);

        $this->service = $this->objectManager->getObject(
            '\Magento\Checkout\Service\V1\Cart\WriteService',
            [
                'quoteFactory' => $this->quoteFactoryMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    public function testCreate()
    {
        $storeId = 345;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['setStoreId', 'save', '__wakeup'], [], '', false);
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->once())->method('setStoreId')->with($storeId);
        $quoteMock->expects($this->once())->method('save');

        $this->service->create();
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Cannot create quote
     */
    public function testCreateWithException()
    {
        $storeId = 345;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['setStoreId', 'save', '__wakeup'], [], '', false);
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->once())->method('setStoreId')->with($storeId);
        $quoteMock->expects($this->once())->method('save')
            ->will($this->throwException(new CouldNotSaveException('Cannot create quote')));

        $this->service->create();
    }
}