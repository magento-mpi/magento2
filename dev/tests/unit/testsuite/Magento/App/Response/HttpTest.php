<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Response;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Response\Http
     */
    protected $_model;

    /**
     * @var \Magento\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookieMock;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $_context;

    protected function setUp()
    {
        $this->_cookieMock = $this->getMock('Magento\Stdlib\Cookie', array(), array(), '', false);
        $this->_context = new \Magento\App\Http\Context();
        $this->_model = new Http($this->_cookieMock, $this->_context);
        $this->_model->headersSentThrowsException = false;
        $this->_model->setHeader('name', 'value');
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetHeaderWhenHeaderNameIsEqualsName()
    {
        $expected = array('name' => 'Name', 'value' => 'value', 'replace' => false);
        $actual = $this->_model->getHeader('Name');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHeaderWhenHeaderNameIsNotEqualsName()
    {
        $this->assertFalse($this->_model->getHeader('Test'));
    }

    public function testSendVary()
    {
        $vary = array('some-vary-key' => 'some-vary-value');
        $expected = sha1(serialize($vary));

        $this->_context->setValue('some-vary-key', 'some-vary-value');
        $this->_cookieMock
            ->expects($this->once())
            ->method('set')
            ->with(Http::COOKIE_VARY_STRING, $expected);
        $this->_model->sendVary();
    }

    /**
     * Test setting public cache headers
     */
    public function testSetPublicHeaders()
    {
        $ttl = 120;
        $pragma = 'cache';
        $cacheControl = 'public, max-age=' . $ttl . ', s-maxage=' . $ttl;
        $between = 1000;

        $this->_model->setPublicHeaders($ttl);
        $this->assertEquals($pragma, $this->_model->getHeader('Pragma')['value']);
        $this->assertEquals($cacheControl, $this->_model->getHeader('Cache-Control')['value']);
        $expiresResult = time($this->_model->getHeader('Expires')['value']);
        $this->assertTrue($expiresResult > $between || $expiresResult < $between);
    }


    /**
     * Test for setting public headers without time to live parameter
     */
    public function testSetPublicHeadersWithoutTtl()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Time to live is a mandatory parameter for set public headers'
        );
        $this->_model->setPublicHeaders(null);
    }

    /**
     * Test setting public cache headers
     */
    public function testSetPrivateHeaders()
    {
        $ttl = 120;
        $pragma = 'cache';
        $cacheControl = 'private, max-age=' . $ttl;
        $expires = gmdate('D, d M Y H:i:s T', strtotime('+' . $ttl . ' seconds'));

        $this->_model->setPrivateHeaders($ttl);
        $this->assertEquals($pragma, $this->_model->getHeader('Pragma')['value']);
        $this->assertEquals($cacheControl, $this->_model->getHeader('Cache-Control')['value']);
        $this->assertEquals($expires, $this->_model->getHeader('Expires')['value']);
    }

    /**
     * Test for setting public headers without time to live parameter
     */
    public function testSetPrivateHeadersWithoutTtl()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Time to live is a mandatory parameter for set private headers'
        );
        $this->_model->setPrivateHeaders(null);
    }

    /**
     * Test setting public cache headers
     */
    public function testSetNoCacheHeaders()
    {
        $pragma = 'no-cache';
        $cacheControl = 'no-store, no-cache, must-revalidate, max-age=0';
        $expires = gmdate('D, d M Y H:i:s T', strtotime('-1 year'));

        $this->_model->setNoCacheHeaders();
        $this->assertEquals($pragma, $this->_model->getHeader('Pragma')['value']);
        $this->assertEquals($cacheControl, $this->_model->getHeader('Cache-Control')['value']);
        $this->assertEquals($expires, $this->_model->getHeader('Expires')['value']);
    }

}
