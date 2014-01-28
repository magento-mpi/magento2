<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App\FrontController;

class HeaderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\App\FrontController\HeaderPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\PageCache\Model\Version|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $versionMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->versionMock = $this->getMockBuilder('Magento\PageCache\Model\Version')
            ->disableOriginalConstructor()
            ->getMock();
        $this->plugin = new HeaderPlugin($this->layoutMock, $this->versionMock);
    }

    /**
     * Test if layout is not cacheable
     */
    public function testAfterDispatchNotCacheable()
    {
        $pragma = 'no-cache';
        $cacheControl = 'no-store, no-cache, must-revalidate, max-age=0';

        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(false));

        $this->responseMock->expects($this->at(0))
            ->method('setHeader')
            ->with($this->equalTo('pragma'), $this->equalTo($pragma), $this->equalTo(true));
        $this->responseMock->expects($this->at(1))
            ->method('setHeader')
            ->with($this->equalTo('cache-control'), $this->equalTo($cacheControl), $this->equalTo(true));
        $this->responseMock->expects($this->at(2))
            ->method('setHeader')
            ->with($this->equalTo('expires'));

        $this->versionMock->expects($this->once())->method('process');

        $this->plugin->afterDispatch($this->responseMock);
    }

    /**
     * Testing that `cache-control` already exists
     */
    public function testAfterDispatchPrivateCache()
    {
        $pragma = 'cache';

        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));

        $this->responseMock->expects($this->at(0))
            ->method('setHeader')
            ->with($this->equalTo('pragma'), $this->equalTo($pragma), $this->equalTo(true));
        $this->responseMock->expects($this->at(1))
            ->method('getHeader')
            ->with($this->equalTo('cache-control'))
            ->will($this->returnValue(true));

        $this->versionMock->expects($this->once())->method('process');

        $this->plugin->afterDispatch($this->responseMock);
    }

    /**
     * Test setting public headers
     */
    public function testAfterDispatchPublicCache()
    {
        $maxAge = \Magento\PageCache\Helper\Data::MAX_AGE_CACHE;
        $pragma = 'cache';
        $cacheControl = 'public, max-age=' . $maxAge;

        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));

        $this->responseMock->expects($this->at(0))
            ->method('setHeader')
            ->with($this->equalTo('pragma'), $this->equalTo($pragma), $this->equalTo(true));
        $this->responseMock->expects($this->at(1))
            ->method('getHeader')
            ->with($this->equalTo('cache-control'))
            ->will($this->returnValue(false));
        $this->responseMock->expects($this->at(2))
            ->method('setHeader')
            ->with($this->equalTo('cache-control'), $this->equalTo($cacheControl), $this->equalTo(true));
        $this->responseMock->expects($this->at(3))
            ->method('setHeader')
            ->with($this->equalTo('expires'));

        $this->versionMock->expects($this->once())->method('process');

        $this->plugin->afterDispatch($this->responseMock);
    }
}
