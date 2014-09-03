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

    protected function setUp()
    {
        $this->quoteFactoryMock = $this->getMock('\Magento\Sales\Model\QuoteFactory', ['create'], [], '', false);
        $this->quoteMock =
            $this->getMock(
                '\Magento\Sales\Model\Quote',
                ['setStoreId', 'load', 'getId', '__wakeup', 'getIsActive'],
                [],
                '',
                false
            );
        $this->quoteLoader = new QuoteLoader($this->quoteFactoryMock);
    }

    public function testLoadWithId()
    {
        $storeId = 1;
        $cartId = 45;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('setStoreId')->with($storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('load')->with($cartId);
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(33));
        $this->quoteMock->expects($this->once())->method('getIsActive')->will($this->returnValue(true));

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
}
