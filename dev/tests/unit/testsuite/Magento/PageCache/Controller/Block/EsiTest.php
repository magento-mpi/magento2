<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Controller\Block;

class EsiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\App\View|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\PageCache\Controller\Block
     */
    protected $action;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->layoutMock = $this->getMockBuilder(
            'Magento\Framework\View\Layout'
        )->disableOriginalConstructor()->getMock();

        $contextMock =
            $this->getMockBuilder('Magento\Framework\App\Action\Context')->disableOriginalConstructor()->getMock();

        $this->requestMock = $this->getMockBuilder(
            'Magento\Framework\App\Request\Http'
        )->disableOriginalConstructor()->getMock();
        $this->responseMock = $this->getMockBuilder(
            'Magento\Framework\App\Response\Http'
        )->disableOriginalConstructor()->getMock();
        $this->viewMock = $this->getMockBuilder('Magento\Framework\App\View')->disableOriginalConstructor()->getMock();

        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->any())->method('getView')->will($this->returnValue($this->viewMock));

        $this->action = new \Magento\PageCache\Controller\Block\Esi($contextMock);
    }

    /**
     * @dataProvider executeDataProvider
     * @param string $blockClass
     * @param bool $shouldSetHeaders
     */
    public function testExecute($blockClass, $shouldSetHeaders)
    {
        $block = 'block';
        $handles = array('handle1', 'handle2');
        $html = 'some-html';
        $mapData = array(array('blocks', '', json_encode(array($block))), array('handles', '', json_encode($handles)));

        $blockInstance1 = $this->getMock(
            $blockClass,
            array('toHtml'),
            array(),
            '',
            false
        );

        $blockInstance1->expects($this->once())->method('toHtml')->will($this->returnValue($html));
        $blockInstance1->setTtl(360);

        $this->requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($mapData));

        $this->viewMock->expects($this->once())->method('loadLayout')->with($this->equalTo($handles));

        $this->viewMock->expects($this->once())->method('getLayout')->will($this->returnValue($this->layoutMock));

        $this->layoutMock->expects(
            $this->once()
        )->method(
                'getBlock'
            )->with(
                $this->equalTo($block)
            )->will(
                $this->returnValue($blockInstance1)
            );

        if ($shouldSetHeaders) {
            $this->responseMock->expects($this->once())
                ->method('setHeader')
                ->with('X-Magento-Tags', implode(',', $blockInstance1->getIdentities()));
        } else {
            $this->responseMock->expects($this->never())
                ->method('setHeader');
        }

        $this->responseMock->expects($this->once())
            ->method('appendBody')
            ->with($this->equalTo($html));

        $this->action->execute();
    }

    public function executeDataProvider()
    {
        return array(
            array('Magento\PageCache\Block\Controller\StubBlock', true),
            array('Magento\Framework\View\Element\AbstractBlock', false),
        );
    }

    public function testExecuteBlockNotExists()
    {
        $handles = json_encode(array('handle1', 'handle2'));
        $mapData = array(
            array('blocks', '', null),
            array('handles', '', $handles)
        );

        $this->requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($mapData));
        $this->viewMock->expects($this->never())->method('getLayout')->will($this->returnValue($this->layoutMock));

        $this->action->execute();
    }
}
