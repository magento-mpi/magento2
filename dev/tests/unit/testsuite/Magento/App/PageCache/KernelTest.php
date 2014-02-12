<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\PageCache;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var \Magento\App\PageCache\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \Magento\App\PageCache\Identifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $identifierMock;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->cacheMock = $this->getMock('Magento\App\PageCache\Cache', array(), array(), '', false);
        $this->identifierMock = $this->getMock('Magento\App\PageCache\Identifier', array(), array(), '', false);
        $this->kernel = new Kernel($this->cacheMock, $this->identifierMock);
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
    }

    /**
     * @dataProvider loadProvider
     * @param string $key
     * @param mixed $value
     */
    public function testLoad($key, $value)
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo($key))
            ->will($this->returnValue(serialize($value)));
        $this->identifierMock
            ->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($key));
        $this->assertEquals($value, $this->kernel->load());
    }

    /**
     * @return array
     */
    public function loadProvider()
    {
        return array(
            array('existing key', array(1, 2, 3)),
            array('existing key', new \Magento\Object(array(1, 2, 3))),
            array('non existing key', false),
        );
    }

    public function testProcessSaveCache()
    {
        $cacheControlHeader = 'public, max-age=100, s-maxage=100';
        $httpCode = 200;
        $this->responseMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('Cache-Control')
            ->will($this->returnValue(array('value' => $cacheControlHeader)));
        $this->responseMock
            ->expects($this->once())
            ->method('getHttpResponseCode')
            ->will($this->returnValue($httpCode));
        $this->responseMock
            ->expects($this->once())
            ->method('setNoCacheHeaders');
        $this->responseMock
            ->expects($this->once())
            ->method('clearHeader')
            ->with($this->equalTo('Set-Cookie'));
        $this->cacheMock
            ->expects($this->once())
            ->method('save');
        $this->kernel->process($this->responseMock);
    }

    /**
     * @dataProvider processNotSaveCacheProvider
     * @param string $cacheControlHeader
     * @param int $httpCode
     * @param bool $overrideHeaders
     */
    public function testProcessNotSaveCache($cacheControlHeader, $httpCode, $overrideHeaders)
    {
        $this->responseMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('Cache-Control')
            ->will($this->returnValue(array('value' => $cacheControlHeader)));
        $this->responseMock
            ->expects($this->any())
            ->method('getHttpResponseCode')
            ->will($this->returnValue($httpCode));
        if ($overrideHeaders) {
            $this->responseMock
                ->expects($this->once())
                ->method('setNoCacheHeaders');
            $this->responseMock
                ->expects($this->once())
                ->method('clearHeader')
                ->with('Set-Cookie');
        }
        $this->cacheMock
            ->expects($this->never())
            ->method('save');
        $this->kernel->process($this->responseMock);
    }

    /**
     * @return array
     */
    public function processNotSaveCacheProvider()
    {
        return array(
            array('private, max-age=100', 200, false),
            array('private, max-age=100', 404, false),
            array('private, max-age=100', 500, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 200, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 404, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 500, false),
            array('public, max-age=100, s-maxage=100', 404, true),
            array('public, max-age=100, s-maxage=100', 500, true),
        );
    }
}
