<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogContextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Search\Model\Layer\Search\Context
     */
    protected $model;

    protected function setUp()
    {
        $this->catalogContextMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Search\Context', array(), array(), '', false
        );
        $this->collProviderMock = $this->getMock(
            '\Magento\Search\Model\Layer\Search\ItemCollectionProvider', array(), array(), '', false
        );
        $this->helperMock = $this->getMock(
            '\Magento\Search\Helper\Data', array(), array(), '', false
        );

        $this->model = new Context($this->catalogContextMock, $this->collProviderMock, $this->helperMock);
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\Context::getCollectionProvider
     * @covers \Magento\Search\Model\Layer\Search\Context::__construct
     */
    public function testGetCollectionProviderEngineAvailable()
    {
        $this->helperMock->expects($this->once())
            ->method('isThirdPartSearchEngine')
            ->will($this->returnValue(true));

        $this->helperMock->expects($this->once())
            ->method('isActiveEngine')
            ->will($this->returnValue(true));

        $this->catalogContextMock->expects($this->never())
            ->method('getCollectionProvider');

        $this->assertInstanceOf(
            '\Magento\Search\Model\Layer\Search\ItemCollectionProvider', $this->model->getCollectionProvider()
        );
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\Context::getCollectionProvider
     */
    public function testGetCollectionProviderEngineUnavailable()
    {
        $this->helperMock->expects($this->once())
            ->method('isThirdPartSearchEngine')
            ->will($this->returnValue(false));

        $this->helperMock->expects($this->any())
            ->method('isActiveEngine')
            ->will($this->returnValue(false));

        $this->catalogContextMock->expects($this->once())
            ->method('getCollectionProvider')
            ->will($this->returnValue($this->collProviderMock));

        $this->assertInstanceOf(
            '\Magento\Search\Model\Layer\Search\ItemCollectionProvider', $this->model->getCollectionProvider()
        );
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\Context::getStateKey
     */
    public function testGetStateKey()
    {
        $this->catalogContextMock->expects($this->once())
            ->method('getStateKey')
            ->will($this->returnValue('key'));

        $this->assertEquals('key', $this->model->getStateKey());
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\Context::getCollectionFilter
     */
    public function testGetCollectionFilter()
    {
        $this->catalogContextMock->expects($this->once())
            ->method('getCollectionFilter')
            ->will($this->returnValue('filter'));

        $this->assertEquals('filter', $this->model->getCollectionFilter());
    }
}
