<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Core_Model_Url_SecurityInfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var Magento_Core_Model_Url_SecurityInfo
     */
    protected $_model;

    protected function setUp()
    {
        $this->_storeMock = $this->getMock('Magento_Core_Model_Store', array('getConfig'), array(), '', false);
        $storeManagerMock = $this->getMock('Magento_Core_Model_StoreManagerInterface');
        $storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_model = new Magento_Core_Model_Url_SecurityInfo(
            $storeManagerMock, array('/account', '/cart')
        );
    }

    public function testIsSecureReturnsFalseIfDisabledInConfig()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(false));
        $this->assertFalse($this->_model->isSecure('http://example.com/account'));
    }

    /**
     * @param string $url
     * @param bool $expected
     * @dataProvider secureUrlDataProvider
     */
    public function testIsSecureChecksIfUrlIsInSecureList($url, $expected)
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(true));
        $this->assertEquals($expected, $this->_model->isSecure($url));
    }

    public function secureUrlDataProvider()
    {
        return array(
            array('/account', true),
            array('/product', false),
            array('/product/12312', false),
            array('/cart', true),
            array('/cart/add', true),
        );
    }
}
