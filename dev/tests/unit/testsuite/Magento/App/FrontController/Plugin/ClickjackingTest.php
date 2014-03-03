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
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;


    protected function setUp()
    {
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->plugin = new \Magento\App\FrontController\Plugin\Clickjacking();
    }

    public function testAfterDispatchIfHeaderExist()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('X-Frame-Options')
            ->will($this->returnValue(false));
        $this->responseMock
            ->expects($this->once())
            ->method('setHeader')
            ->with('X-Frame-Options', 'SAMEORIGIN')
            ->will($this->returnValue($this->responseMock));
        $this->assertEquals($this->responseMock, $this->plugin->afterDispatch($this->subjectMock, $this->responseMock));
    }

    public function testAfterDispatchIfHeaderNotExist()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('X-Frame-Options')
            ->will($this->returnValue(true));
        $this->responseMock
            ->expects($this->never())
            ->method('setHeader');
        $this->assertEquals($this->responseMock, $this->plugin->afterDispatch($this->subjectMock, $this->responseMock));
    }
}