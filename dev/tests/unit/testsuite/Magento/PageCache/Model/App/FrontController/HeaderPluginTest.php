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

use Magento\PageCache\Helper\Data;

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
     * @var \Magento\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;
    
    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\PageCache\Model\Version|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $versionMock;

    /**
     * @var \Magento\PageCache\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->configMock = $this->getMock('Magento\App\ConfigInterface', array(), array(), '', false);
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->versionMock = $this->getMockBuilder('Magento\PageCache\Model\Version')
            ->disableOriginalConstructor()
            ->getMock();
        $this->subjectMock = $this->getMock('Magento\App\FrontControllerInterface');
        $this->plugin = new HeaderPlugin($this->layoutMock, $this->configMock, $this->versionMock);
    }

    /**
     * Test if layout is not cacheable
     */
    public function testAfterDispatchNotCacheable()
    {
        $pragma = 'no-cache';
        $cacheControl = 'no-store, no-cache, must-revalidate, max-age=0';

        $this->layoutMock->expects($this->once())
            ->method('isPrivate')
            ->will($this->returnValue(false));

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

        $result = $this->plugin->afterDispatch($this->subjectMock, $this->responseMock);
        $this->assertInstanceOf('Magento\App\ResponseInterface', $result);
    }

    /**
     * Testing that `cache-control` already exists
     */
    public function testAfterDispatchPrivateCache()
    {
        $pragma = 'cache';
        $maxAge = Data::PRIVATE_MAX_AGE_CACHE;
        $cacheControl = 'private, max-age=' . $maxAge;

        $this->layoutMock->expects($this->once())
            ->method('isPrivate')
            ->will($this->returnValue(true));

        $this->responseMock->expects($this->at(0))
            ->method('setHeader')
            ->with($this->equalTo('pragma'), $this->equalTo($pragma), $this->equalTo(true));
        $this->responseMock->expects($this->at(1))
            ->method('setHeader')
            ->with($this->equalTo('cache-control'), $this->equalTo($cacheControl), $this->equalTo(true));
        $this->responseMock->expects($this->at(2))
            ->method('setHeader')
            ->with($this->equalTo('expires'));

        $this->layoutMock->expects($this->never())->method('isCacheable');
        $this->versionMock->expects($this->never())->method('process');

        $result = $this->plugin->afterDispatch($this->subjectMock, $this->responseMock);
        $this->assertInstanceOf('Magento\App\ResponseInterface', $result);
    }

    /**
     * Test setting public headers
     */
    public function testAfterDispatchPublicCache()
    {
        $maxAge = 120;
        $pragma = 'cache';
        $cacheControl = 'public, max-age=' . $maxAge . ', s-maxage=' . $maxAge;

        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with($this->equalTo(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL))
            ->will($this->returnValue($maxAge));

        $this->layoutMock->expects($this->once())
            ->method('isPrivate')
            ->will($this->returnValue(false));

        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));

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

        $result = $this->plugin->afterDispatch($this->subjectMock, $this->responseMock);
        $this->assertInstanceOf('Magento\App\ResponseInterface', $result);
    }
}
