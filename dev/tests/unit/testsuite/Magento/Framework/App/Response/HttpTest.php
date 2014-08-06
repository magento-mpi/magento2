<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Response;

use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\CookieManager
     */
    protected $_cookieManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactoryMock;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Http\Context
     */
    protected $_contextMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_cookieMetadataFactoryMock = $this->getMockBuilder(
            'Magento\Framework\Stdlib\Cookie\CookieMetadataFactory'
        )->disableOriginalConstructor()->getMock();
        $this->_cookieManagerMock = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManager')
            ->disableOriginalConstructor()->getMock();
        $this->_contextMock = $this->getMockBuilder('Magento\Framework\App\Http\Context')->disableOriginalConstructor()
            ->getMock();
        $this->_model = $objectManager->getObject(
            'Magento\Framework\App\Response\Http',
            [
                'cookieManager' => $this->_cookieManagerMock,
                'cookieMetadataFactory' => $this->_cookieMetadataFactoryMock,
                'context' => $this->_contextMock
            ]
        );
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
        $data = ['some-vary-key' => 'some-vary-value'];
        $expectedCookieName = Http::COOKIE_VARY_STRING;
        $expectedCookieValue = sha1(serialize($data));
        $publicCookieMetadataMock = $this->getMock('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata');
        $this->_contextMock->expects($this->once())
            ->method('getData')
            ->with()
            ->will(
                $this->returnValue($data)
            );

        $this->_cookieMetadataFactoryMock->expects($this->once())
            ->method('createPublicCookieMetadata')
            ->with(
                [
                    PublicCookieMetadata::KEY_DURATION => null,
                    PublicCookieMetadata::KEY_PATH => '/'
                ]
            )
            ->will(
                $this->returnValue($publicCookieMetadataMock)
            );
        $this->_cookieManagerMock->expects($this->once())
            ->method('setPublicCookie')
            ->with($expectedCookieName, $expectedCookieValue, $publicCookieMetadataMock);
        $this->_model->sendVary();
    }

    public function testSendVaryEmptyData()
    {
        $expectedCookieName = Http::COOKIE_VARY_STRING;
        $cookieMetadataMock = $this->getMock('Magento\Framework\Stdlib\Cookie\CookieMetadata');

        $this->_cookieMetadataFactoryMock->expects($this->once())
            ->method('createCookieMetadata')
            ->with(
                [
                    CookieMetadata::KEY_PATH => '/'
                ]
            )
            ->will(
                $this->returnValue($cookieMetadataMock)
            );
        $this->_cookieManagerMock->expects($this->once())
            ->method('deleteCookie')
            ->with($expectedCookieName, $cookieMetadataMock);
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

    /**
     * Test setting body in JSON format
     */
    public function testRepresentJson()
    {
        $this->_model->setHeader('Content-Type', 'text/javascript');
        $this->_model->representJson('json_string');
        $this->assertEquals('application/json', $this->_model->getHeader('Content-Type')['value']);
        $this->assertEquals('json_string', $this->_model->getBody('default'));
    }
}
