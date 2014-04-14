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
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

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
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->kernel = new Kernel($this->cacheMock, $this->identifierMock, $this->requestMock);
        $this->responseMock = $this->getMockBuilder(
            'Magento\App\Response\Http'
        )->setMethods(
            array('getHeader', 'getHttpResponseCode', 'setNoCacheHeaders', 'clearHeader')
        )->disableOriginalConstructor()->getMock();
    }

    /**
     * @dataProvider loadProvider
     * @param mixed $expected
     * @param string $id
     * @param mixed $cache
     * @param bool $isGet
     * @param bool $isHead
     */
    public function testLoad($expected, $id, $cache, $isGet, $isHead)
    {
        $this->requestMock->expects($this->once())->method('isGet')->will($this->returnValue($isGet));
        $this->requestMock->expects($this->any())->method('isHead')->will($this->returnValue($isHead));
        $this->cacheMock->expects(
            $this->any()
        )->method(
            'load'
        )->with(
            $this->equalTo($id)
        )->will(
            $this->returnValue(serialize($cache))
        );
        $this->identifierMock->expects($this->any())->method('getValue')->will($this->returnValue($id));
        $this->assertEquals($expected, $this->kernel->load());
    }

    /**
     * @return array
     */
    public function loadProvider()
    {
        $data = array(1, 2, 3);
        return array(
            array($data, 'existing key', $data, true, false),
            array($data, 'existing key', $data, false, true),
            array(new \Magento\Object($data), 'existing key', new \Magento\Object($data), true, false),
            array(false, 'existing key', $data, false, false),
            array(false, 'non existing key', false, true, false),
            array(false, 'non existing key', false, false, false)
        );
    }

    public function testProcessSaveCache()
    {
        $cacheControlHeader = 'public, max-age=100, s-maxage=100';
        $httpCode = 200;

        $this->responseMock->expects(
            $this->at(0)
        )->method(
            'getHeader'
        )->with(
            'Cache-Control'
        )->will(
            $this->returnValue(array('value' => $cacheControlHeader))
        );
        $this->responseMock->expects(
            $this->once()
        )->method(
            'getHttpResponseCode'
        )->will(
            $this->returnValue($httpCode)
        );
        $this->requestMock->expects($this->once())->method('isGet')->will($this->returnValue(true));
        $this->responseMock->expects($this->once())->method('setNoCacheHeaders');
        $this->responseMock->expects($this->at(3))->method('getHeader')->with('X-Magento-Tags');
        $this->responseMock->expects($this->at(4))->method('clearHeader')->with($this->equalTo('Set-Cookie'));
        $this->responseMock->expects($this->at(5))->method('clearHeader')->with($this->equalTo('X-Magento-Tags'));
        $this->cacheMock->expects($this->once())->method('save');
        $this->kernel->process($this->responseMock);
    }

    /**
     * @dataProvider processNotSaveCacheProvider
     * @param string $cacheControlHeader
     * @param int $httpCode
     * @param bool $isGet
     * @param bool $overrideHeaders
     */
    public function testProcessNotSaveCache($cacheControlHeader, $httpCode, $isGet, $overrideHeaders)
    {
        $this->responseMock->expects(
            $this->once()
        )->method(
            'getHeader'
        )->with(
            'Cache-Control'
        )->will(
            $this->returnValue(array('value' => $cacheControlHeader))
        );
        $this->responseMock->expects($this->any())->method('getHttpResponseCode')->will($this->returnValue($httpCode));
        $this->requestMock->expects($this->any())->method('isGet')->will($this->returnValue($isGet));
        if ($overrideHeaders) {
            $this->responseMock->expects($this->once())->method('setNoCacheHeaders');
        }
        $this->cacheMock->expects($this->never())->method('save');
        $this->kernel->process($this->responseMock);
    }

    /**
     * @return array
     */
    public function processNotSaveCacheProvider()
    {
        return array(
            array('private, max-age=100', 200, true, false),
            array('private, max-age=100', 200, false, false),
            array('private, max-age=100', 404, true, false),
            array('private, max-age=100', 500, true, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 200, true, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 200, false, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 404, true, false),
            array('no-store, no-cache, must-revalidate, max-age=0', 500, true, false),
            array('public, max-age=100, s-maxage=100', 404, true, true),
            array('public, max-age=100, s-maxage=100', 500, true, true),
            array('public, max-age=100, s-maxage=100', 200, false, true)
        );
    }
}
