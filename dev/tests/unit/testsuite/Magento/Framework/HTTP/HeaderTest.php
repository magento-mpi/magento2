<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Http;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $_converter;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_request = $this->getMock(
            'Magento\Framework\App\Request\Http',
            array('getServer', 'getRequestUri'),
            array(),
            '',
            false
        );

        $this->_converter = $this->getMock('\Magento\Framework\Stdlib\String', array('cleanString'));
    }

    /**
     * @param string $method
     * @param boolean $clean
     * @param string $expectedValue
     *
     * @dataProvider methodsDataProvider
     *
     * @covers \Magento\Framework\HTTP\Header::getHttpHost
     * @covers \Magento\Framework\HTTP\Header::getHttpUserAgent
     * @covers \Magento\Framework\HTTP\Header::getHttpAcceptLanguage
     * @covers \Magento\Framework\HTTP\Header::getHttpAcceptCharset
     * @covers \Magento\Framework\HTTP\Header::getHttpReferer
     */
    public function testHttpMethods($method, $clean, $expectedValue)
    {
        $this->_request->expects($this->once())->method('getServer')->will($this->returnValue('value'));

        $this->_prepareCleanString($clean);

        $headerObject = $this->_objectManager->getObject(
            '\Magento\Framework\HTTP\Header',
            array('httpRequest' => $this->_request, 'converter' => $this->_converter)
        );

        $method = new \ReflectionMethod('\Magento\Framework\HTTP\Header', $method);
        $result = $method->invokeArgs($headerObject, array('clean' => $clean));

        $this->assertEquals($expectedValue, $result);
    }

    /**
     * @return array
     */
    public function methodsDataProvider()
    {
        return array(
            'getHttpHost clean true' => array(
                'method' => 'getHttpHost',
                'clean' => true,
                'expectedValue' => 'converted value'
            ),
            'getHttpHost clean false' => array(
                'method' => 'getHttpHost',
                'clean' => false,
                'expectedValue' => 'value'
            ),
            'getHttpUserAgent clean true' => array(
                'method' => 'getHttpUserAgent',
                'clean' => true,
                'expectedValue' => 'converted value'
            ),
            'getHttpUserAgent clean false' => array(
                'method' => 'getHttpUserAgent',
                'clean' => false,
                'expectedValue' => 'value'
            ),
            'getHttpAcceptLanguage clean true' => array(
                'method' => 'getHttpAcceptLanguage',
                'clean' => true,
                'expectedValue' => 'converted value'
            ),
            'getHttpAcceptLanguage clean false' => array(
                'method' => 'getHttpAcceptLanguage',
                'clean' => false,
                'expectedValue' => 'value'
            ),
            'getHttpAcceptCharset clean true' => array(
                'method' => 'getHttpAcceptCharset',
                'clean' => true,
                'expectedValue' => 'converted value'
            ),
            'getHttpAcceptCharset clean false' => array(
                'method' => 'getHttpAcceptCharset',
                'clean' => false,
                'expectedValue' => 'value'
            ),
            'getHttpReferer clean true' => array(
                'method' => 'getHttpReferer',
                'clean' => true,
                'expectedValue' => 'converted value'
            ),
            'getHttpReferer clean false' => array(
                'method' => 'getHttpReferer',
                'clean' => false,
                'expectedValue' => 'value'
            )
        );
    }

    /**
     * @param boolean $clean
     * @param string $expectedValue
     *
     * @dataProvider getRequestUriDataProvider
     */
    public function testGetRequestUri($clean, $expectedValue)
    {
        $this->_request->expects($this->once())->method('getRequestUri')->will($this->returnValue('value'));

        $this->_prepareCleanString($clean);

        $headerObject = $this->_objectManager->getObject(
            '\Magento\Framework\HTTP\Header',
            array('httpRequest' => $this->_request, 'converter' => $this->_converter)
        );

        $result = $headerObject->getRequestUri($clean);

        $this->assertEquals($expectedValue, $result);
    }

    /**
     * @return array
     */
    public function getRequestUriDataProvider()
    {
        return array(
            'getRequestUri clean true' => array('clean' => true, 'expectedValue' => 'converted value'),
            'getRequestUri clean false' => array('clean' => false, 'expectedValue' => 'value')
        );
    }

    /**
     * @param boolean $clean
     * @return $this
     */
    protected function _prepareCleanString($clean)
    {
        $cleanStringExpects = $clean ? $this->once() : $this->never();

        $this->_converter->expects(
            $cleanStringExpects
        )->method(
            'cleanString'
        )->will(
            $this->returnValue('converted value')
        );
        return $this;
    }
}
