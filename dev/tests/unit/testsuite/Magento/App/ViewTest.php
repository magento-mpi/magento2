<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\View
     */
    protected $_view;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionFlagMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_layoutMock = $this->getMock('Magento\View\Layout', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_layoutProcessor = $this->getMock('Magento\View\Layout\ProcessorInterface');
        $this->_layoutMock->expects(
            $this->any()
        )->method(
            'getUpdate'
        )->will(
            $this->returnValue($this->_layoutProcessor)
        );
        $this->_actionFlagMock = $this->getMock('Magento\App\ActionFlag', array(), array(), '', false);
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface');
        $this->_view = $helper->getObject(
            'Magento\App\View',
            array(
                'layout' => $this->_layoutMock,
                'request' => $this->_requestMock,
                'response' => $this->getMock('Magento\App\Response\Http', array(), array(), '', false),
                'configScope' => $this->_configScopeMock,
                'eventManager' => $this->_eventManagerMock,
                'actionFlag' => $this->_actionFlagMock
            )
        );
    }

    public function testGetLayout()
    {
        $this->assertEquals($this->_layoutMock, $this->_view->getLayout());
    }

    /**
     * @expectedException \RuntimeException
     * @exceptedExceptionMessage 'Layout must be loaded only once.'
     */
    public function testLoadLayoutWhenLayoutAlreadyLoaded()
    {
        $this->_view->setIsLayoutLoaded(true);
        $this->_view->loadLayout();
    }

    public function testLoadLayoutWithDefaultSetup()
    {

        $this->_layoutProcessor->expects($this->at(0))->method('addHandle')->with('default');
        $this->_requestMock->expects(
            $this->any()
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('action_name')
        );
        $this->_layoutMock->expects(
            $this->once()
        )->method(
            'generateXml'
        )->will(
            $this->returnValue($this->_layoutMock)
        );
        $this->_layoutMock->expects(
            $this->once()
        )->method(
            'generateElements'
        )->will(
            $this->returnValue($this->_layoutMock)
        );
        $this->_view->loadLayout();
    }

    public function testLoadLayoutWhenBlocksNotGenerated()
    {
        $this->_layoutMock->expects($this->once())->method('generateXml');
        $this->_layoutMock->expects($this->never())->method('generateElements');
        $this->_view->loadLayout('', false, true);
    }

    public function testLoadLayoutWhenXmlNotGenerated()
    {
        $this->_layoutMock->expects($this->never())->method('generateElements');
        $this->_layoutMock->expects($this->never())->method('generateXml');
        $this->_view->loadLayout('', true, false);
    }

    public function testGetDefaultLayoutHandle()
    {
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('ExpectedValue')
        );
        $this->assertEquals('expectedvalue', $this->_view->getDefaultLayoutHandle());
    }

    public function testAddActionLayoutHandlesWhenPageLayoutHandlesNotExist()
    {
        $defaultHandles = 'full_action_name';
        $this->_requestMock->expects(
            $this->exactly(2)
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('Full_Action_Name')
        );
        $this->_layoutProcessor->expects(
            $this->once()
        )->method(
            'addPageHandles'
        )->with(
            array($defaultHandles)
        )->will(
            $this->returnValue(false)
        );
        $this->_layoutProcessor->expects($this->once())->method('addHandle')->with($defaultHandles);
        $this->_view->addActionLayoutHandles();
    }

    public function testAddActionLayoutHandlesWhenPageLayoutHandlesExist()
    {
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('Full_Action_Name')
        );
        $this->_layoutProcessor->expects(
            $this->once()
        )->method(
            'addPageHandles'
        )->with(
            array('full_action_name')
        )->will(
            $this->returnValue(true)
        );
        $this->_layoutProcessor->expects($this->never())->method('addHandle');
        $this->_view->addActionLayoutHandles();
    }

    public function testAddPageLayoutHandles()
    {
        $pageHandles = array('full_action_name', 'full_action_name_key_value');
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('Full_Action_Name')
        );
        $this->_layoutProcessor->expects($this->once())->method('addPageHandles')->with($pageHandles);
        $this->_view->addPageLayoutHandles(array('key' => 'value'));
    }

    public function testGenerateLayoutBlocksWhenFlagIsNotSet()
    {

        $valueMap = array(
            array('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT, false),
            array('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT, false)
        );
        $this->_actionFlagMock->expects($this->any())->method('get')->will($this->returnValueMap($valueMap));

        $eventArgument = array('full_action_name' => 'Full_Name', 'layout' => $this->_layoutMock);
        $this->_requestMock->expects(
            $this->exactly(2)
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('Full_Name')
        );
        $this->_eventManagerMock->expects(
            $this->at(0)
        )->method(
            'dispatch'
        )->with(
            'controller_action_layout_generate_blocks_before',
            $eventArgument
        );
        $this->_eventManagerMock->expects(
            $this->at(1)
        )->method(
            'dispatch'
        )->with(
            'controller_action_layout_generate_blocks_after',
            $eventArgument
        );
        $this->_view->generateLayoutBlocks();
    }

    public function testGenerateLayoutBlocksWhenFlagIsSet()
    {

        $valueMap = array(
            array('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT, true),
            array('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT, true)
        );
        $this->_actionFlagMock->expects($this->any())->method('get')->will($this->returnValueMap($valueMap));

        $this->_eventManagerMock->expects($this->never())->method('dispatch');
        $this->_view->generateLayoutBlocks();
    }

    public function testRenderLayoutIfActionFlagExist()
    {
        $this->_actionFlagMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            '',
            'no-renderLayout'
        )->will(
            $this->returnValue(true)
        );
        $this->_eventManagerMock->expects($this->never())->method('dispatch');
        $this->_view->renderLayout();
    }

    public function testRenderLayoutWhenOutputNotEmpty()
    {
        $this->_actionFlagMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            '',
            'no-renderLayout'
        )->will(
            $this->returnValue(false)
        );
        $this->_layoutMock->expects($this->once())->method('addOutputElement')->with('output');
        $this->_layoutMock->expects($this->once())->method('getOutput');
        $this->_view->renderLayout('output');
    }

    public function testRenderLayoutWhenOutputEmpty()
    {
        $this->_actionFlagMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            '',
            'no-renderLayout'
        )->will(
            $this->returnValue(false)
        );
        $this->_layoutMock->expects($this->never())->method('addOutputElement');
        $this->_layoutMock->expects($this->once())->method('getOutput');
        $this->_view->renderLayout();
    }
}
