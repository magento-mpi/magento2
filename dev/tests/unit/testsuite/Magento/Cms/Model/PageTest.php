<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model;

/**
 * @covers \Magento\Cms\Model\Page
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $thisMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Magento\Cms\Model\Resource\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourcePageMock;

    protected function setUp()
    {
        $this->eventManagerMock = $this
            ->getMockBuilder('Magento\Framework\Event\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourcePageMock = $this
            ->getMockBuilder('Magento\Cms\Model\Resource\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $v=get_class_methods(get_class($this->resourcePageMock));
        $this->thisMock = $this
            ->getMockBuilder('Magento\Cms\Model\Page')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    '_getResource'
                ]
            )
            ->getMock();

        $this->thisMock
            ->expects($this->any())
            ->method('_getResource')
            ->willReturn($this->resourcePageMock);

        $reflection = new \ReflectionClass($this->thisMock);
        $mathRandomProperty = $reflection->getProperty('_eventManager');
        $mathRandomProperty->setAccessible(true);
        $mathRandomProperty->setValue($this->thisMock, $this->eventManagerMock);
    }

    /**
     * @covers \Magento\Cms\Model\Page::noRoutePage
     */
    public function testNoRoutePage()
    {
        $this->assertEquals($this->thisMock, $this->thisMock->noRoutePage());
    }

    /**
     * @covers \Magento\Cms\Model\Page::checkIdentifier
     */
    public function testCheckIdentifier()
    {
        $identifier = 1;
        $storeId = 2;
        $fetchOneResult = 'some result';

        $this->resourcePageMock
            ->expects($this->atLeastOnce())
            ->method('checkIdentifier')
            ->with($identifier, $storeId)
            ->willReturn($fetchOneResult);

        $this->assertInternalType('string', $this->thisMock->checkIdentifier($identifier, $storeId));
    }
}
