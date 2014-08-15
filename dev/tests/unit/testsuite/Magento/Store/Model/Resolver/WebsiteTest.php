<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Resolver;

/**
 * Test class for \Magento\Store\Model\Resolver\Website
 */
class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Website
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface',
            array(),
            array(),
            '',
            false,
            false
        );

        $this->_model = new Website($this->_storeManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_storeManagerMock);
    }

    public function testGetScope()
    {
        $scopeMock = $this->getMock('Magento\Framework\App\ScopeInterface', array(), array(), '', false, false);
        $this->_storeManagerMock
            ->expects($this->once())
            ->method('getWebsite')
            ->with(0)
            ->will($this->returnValue($scopeMock));

        $this->assertEquals($scopeMock, $this->_model->getScope());
    }

    /**
     * @expectedException \Magento\Framework\App\InitException
     */
    public function testGetScopeWithInvalidScope()
    {
        $scopeMock = new \StdClass();
        $this->_storeManagerMock
            ->expects($this->once())
            ->method('getWebsite')
            ->with(0)
            ->will($this->returnValue($scopeMock));

        $this->assertEquals($scopeMock, $this->_model->getScope());
    }
}
