<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\PageCache\Controller\Block
 */
namespace Magento\PageCache\Controller;

/**
 * Class BlockTest
 *
 * @package Magento\PageCache\Controller
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\App\View|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\PageCache\Controller\Block
     */
    protected $controller;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->layoutMock = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder('Magento\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\App\Response\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewMock = $this->getMockBuilder('Magento\App\View')
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->any())->method('getView')->will($this->returnValue($this->viewMock));

        $this->controller = new \Magento\PageCache\Controller\Block($contextMock);
    }

    public function testRenderActionNotAjax()
    {
        $this->requestMock->expects($this->once())->method('isAjax')->will($this->returnValue(false));
        $this->requestMock->expects($this->once())->method('setActionName')->will($this->returnValue('noroute'));
        $this->requestMock->expects($this->once())->method('setDispatched')->will($this->returnValue(false));
        $result = $this->controller->renderAction();
        $this->assertNull($result);
    }

    /**
     * Test no params: blocks, handles
     */
    public function testRenderActionNoParams()
    {
        $this->requestMock->expects($this->once())->method('isAjax')->will($this->returnValue(true));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with($this->equalTo('blocks'), $this->equalTo([]))
            ->will($this->returnValue([]));
        $this->requestMock->expects($this->at(2))
            ->method('getParam')
            ->with($this->equalTo('handles'), $this->equalTo([]))
            ->will($this->returnValue([]));
        $result = $this->controller->renderAction();
        $this->assertNull($result);
    }

    public function testRenderAction()
    {
        $blocks = array('block1', 'block2');
        $handles = array('handle1', 'handle2');
        $expectedData = array('block1' => 'data1', 'block2' => 'data2');
        // one year
        $maxAge = 365 * 24 * 60 * 60;

        $blockInstance1 = $this->getMockForAbstractClass(
            'Magento\View\Element\AbstractBlock', array(), '', false, true, true, array('toHtml')
        );
        $blockInstance1->expects($this->once())->method('toHtml')->will($this->returnValue($expectedData['block1']));

        $blockInstance2 = $this->getMockForAbstractClass(
            'Magento\View\Element\AbstractBlock', array(), '', false, true, true, array('toHtml')
        );
        $blockInstance2->expects($this->once())->method('toHtml')->will($this->returnValue($expectedData['block2']));

        $this->requestMock->expects($this->once())->method('isAjax')->will($this->returnValue(true));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with($this->equalTo('blocks'), $this->equalTo([]))
            ->will($this->returnValue($blocks));
        $this->requestMock->expects($this->at(2))
            ->method('getParam')
            ->with($this->equalTo('handles'), $this->equalTo([]))
            ->will($this->returnValue($handles));
        $this->viewMock->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo($handles));
        $this->viewMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layoutMock));
        $this->layoutMock->expects($this->at(0))
            ->method('getBlock')
            ->with($this->equalTo($blocks[0]))
            ->will($this->returnValue($blockInstance1));
        $this->layoutMock->expects($this->at(1))
            ->method('getBlock')
            ->with($this->equalTo($blocks[1]))
            ->will($this->returnValue($blockInstance2));

        $this->responseMock->expects($this->once())
            ->method('appendBody')
            ->with($this->equalTo(json_encode($expectedData)));

        $this->controller->renderAction();
    }
}
