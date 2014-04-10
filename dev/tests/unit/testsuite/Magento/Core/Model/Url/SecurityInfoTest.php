<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Url;

class SecurityInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfigMock;

    /**
     * @var \Magento\Core\Model\Url\SecurityInfo
     */
    protected $_model;

    protected function setUp()
    {
        $this->_scopeConfigMock = $this->getMock('\Magento\App\Config\ScopeConfigInterface');
        $this->_model = new \Magento\Core\Model\Url\SecurityInfo($this->_scopeConfigMock, array('/account', '/cart'));
    }

    public function testIsSecureReturnsFalseIfDisabledInConfig()
    {
        $this->_scopeConfigMock->expects($this->once())->method('getValue')->will($this->returnValue(false));
        $this->assertFalse($this->_model->isSecure('http://example.com/account'));
    }

    /**
     * @param string $url
     * @param bool $expected
     * @dataProvider secureUrlDataProvider
     */
    public function testIsSecureChecksIfUrlIsInSecureList($url, $expected)
    {
        $this->_scopeConfigMock->expects($this->once())->method('getValue')->will($this->returnValue(true));
        $this->assertEquals($expected, $this->_model->isSecure($url));
    }

    public function secureUrlDataProvider()
    {
        return array(
            array('/account', true),
            array('/product', false),
            array('/product/12312', false),
            array('/cart', true),
            array('/cart/add', true)
        );
    }
}
