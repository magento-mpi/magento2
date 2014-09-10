<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

class QuoteRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QuoteRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    protected function setUp()
    {
        $objectManager =new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->quoteFactoryMock = $this->getMock('\Magento\Sales\Model\QuoteFactory', ['create'], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->quoteMock =
            $this->getMock('\Magento\Sales\Model\Quote', ['load', 'getIsActive', 'getId', '__wakeup'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->model = $objectManager->getObject(
            'Magento\Sales\Model\QuoteRepository',
            [
                'quoteFactory' => $this->quoteFactoryMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with cartId = 14
     */
    public function testGetWithExceptionById()
    {
        $cartId = 14;

        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($this->storeMock));
        $this->quoteMock->expects($this->once())
            ->method('load')
            ->with($cartId)
            ->will($this->returnValue($this->storeMock));
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(false));

        $this->model->get($cartId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with cartId = 15
     */
    public function testGetWithExceptionByIsActive()
    {
        $cartId = 15;

        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($this->storeMock));
        $this->quoteMock->expects($this->once())
            ->method('load')
            ->with($cartId)
            ->will($this->returnValue($this->storeMock));
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(true));
        $this->quoteMock->expects($this->once())->method('getIsActive')->will($this->returnValue(0));

        $this->model->get($cartId);
    }

    public function testGet()
    {
        $cartId = 15;

        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($this->storeMock));
        $this->quoteMock->expects($this->once())
            ->method('load')
            ->with($cartId)
            ->will($this->returnValue($this->storeMock));
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(true));
        $this->quoteMock->expects($this->once())->method('getIsActive')->will($this->returnValue(1));

        $this->assertEquals($this->quoteMock, $this->model->get($cartId));
    }
}

