<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\FrontController\Plugin;

class ClickjackingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\App\FrontController\Plugin\Clickjacking
     */
    protected $_plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;


    protected function setUp()
    {
        $this->_responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->_plugin = new \Magento\App\FrontController\Plugin\Clickjacking();
    }

    public function testAfterDispatchIfHeaderExist()
    {
        $this->_responseMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('X-Frame-Options')
            ->will($this->returnValue(false));
        $this->_responseMock
            ->expects($this->once())
            ->method('setHeader')
            ->with('X-Frame-Options', 'SAMEORIGIN')
            ->will($this->returnValue($this->_responseMock));
        $this->assertEquals($this->_responseMock, $this->_plugin->afterDispatch($this->_responseMock));
    }

    public function testAfterDispatchIfHeaderNotExist()
    {
        $this->_responseMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('X-Frame-Options')
            ->will($this->returnValue(true));
        $this->_responseMock
            ->expects($this->never())
            ->method('setHeader');
        $this->assertEquals($this->_responseMock, $this->_plugin->afterDispatch($this->_responseMock));
    }
}