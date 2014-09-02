<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1;

class QuoteLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userContextMock;

    protected function setUp()
    {
        $this->quoteFactoryMock = $this->getMock('\Magento\Sales\Model\QuoteFactory', ['create'], [], '', false);
        $this->userContextMock = $this->getMock('\Magento\Authorization\Model\UserContextInterface');
        $this->quoteMock =
            $this->getMock(
                '\Magento\Sales\Model\Quote',
                ['setStoreId', 'load', 'getId', '__wakeup', 'getIsActive', 'getCustomerId'],
                [],
                '',
                false
            );
        $this->quoteLoader = new QuoteLoader($this->quoteFactoryMock, $this->userContextMock);
    }

    public function testLoadWithId()
    {
        $storeId = 1;
        $cartId = 45;
        $customerId = 1;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('setStoreId')->with($storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('load')->with($cartId);
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(33));
        $this->quoteMock->expects($this->once())->method('getIsActive')->will($this->returnValue(true));
        $this->quoteMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->will(
                $this->returnValue(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER)
            );
        $this->userContextMock->expects($this->once())->method('getUserId')->will($this->returnValue($customerId));

        $this->assertEquals($this->quoteMock, $this->quoteLoader->load($cartId, $storeId));
    }

    public function testLoadForAdminUser()
    {
        $storeId = 1;
        $cartId = 45;
        $customerId = 1;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('setStoreId')->with($storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('load')->with($cartId);
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(33));
        $this->quoteMock->expects($this->once())->method('getIsActive')->will($this->returnValue(true));
        $this->quoteMock->expects($this->any())->method('getCustomerId')->will($this->returnValue($customerId));
        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->will(
                $this->returnValue(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN)
            );
        $this->userContextMock->expects($this->any())->method('getUserId')->will($this->returnValue(800));

        $this->assertEquals($this->quoteMock, $this->quoteLoader->load($cartId, $storeId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with cartId = 34
     */
    public function testLoadWithoutId()
    {
        $storeId = 12;
        $cartId = 34;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('setStoreId')->with($storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('load')->with($cartId);
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(false));
        $this->quoteLoader->load($cartId, $storeId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with cartId = 34
     */
    public function testLoadWithInvalidCustomerId()
    {
        $storeId = 12;
        $cartId = 34;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('setStoreId')->with($storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('load')->with($cartId);
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(50));
        $this->quoteMock->expects($this->once())->method('getIsActive')->will($this->returnValue(true));
        $this->quoteMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(10));
        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->will(
                $this->returnValue(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER)
            );
        $this->userContextMock->expects($this->once())->method('getUserId')->will($this->returnValue(20));
        $this->quoteLoader->load($cartId, $storeId);
    }
}
