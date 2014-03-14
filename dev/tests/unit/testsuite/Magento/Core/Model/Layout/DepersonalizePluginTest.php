<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout;

/**
 * Class DepersonalizePluginTest
 */
class DepersonalizePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\DepersonalizePluginTest
     */
    protected $plugin;

    /**
     * @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\Event\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->moduleManagerMock = $this->getMock('Magento\Module\Manager', array(), array(), '', false);
        $this->eventManagerMock = $this->getMock('Magento\Event\Manager', array(), array(), '', false);
        $this->plugin = new \Magento\Core\Model\Layout\DepersonalizePlugin(
            $this->requestMock,
            $this->moduleManagerMock,
            $this->eventManagerMock
        );
    }

    /**
     * Test method afterGenerateXml with enabled module PageCache
     */
    public function testAfterGenerateXmlPageCacheEnabled()
    {
        $expectedResult = 'result';
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with($this->equalTo('Magento_PageCache'))
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->once($this->once()))
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));

        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('depersonalize_clear_session'));

        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test method afterGenerateXml with disabled module PageCache
     */
    public function testAfterGenerateXmlPageCacheDisabled()
    {
        $expectedResult = 'result';
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with($this->equalTo('Magento_PageCache'))
            ->will($this->returnValue(false));
        $this->requestMock->expects($this->never())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertEquals($expectedResult, $actualResult);
    }
}
 