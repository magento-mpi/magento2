<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller\Varien;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Controller\Varien\Action;
     */
    protected $_model;

    /**
     * @var \Magento\Core\Controller\Varien\Action\Context
     */
    protected $_contextMock;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_requestMock;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_responseMock;

    /**
     * @var \Magento\App\FrontController
     */
    protected $_frontController;

    /**
     * @var \Magento\App\ActionInterface
     */
    protected $_actionInterfaceMock;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManagerMock;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Config\ScopeInterface
     */
    protected $_configScopeMock;

    /**
     * @var \Magento\Core\Model\Layout\Factory
     */
    protected $_layoutMock;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translateMock;


    protected function setUp()
    {
        $this->_contextMock = $this->getMock('\Magento\Core\Controller\Varien\Action\Context',
            array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->_contextMock->expects($this->any())->method('getRequest')
            ->will($this->returnValue($this->_requestMock));
        $this->_contextMock->expects($this->any())->method('getResponse')
            ->will($this->returnValue($this->_responseMock));
        $this->_frontController = $this->getMock('\Magento\App\FrontController',
            array('setAction'), array(), '', false );
        $this->_contextMock->expects($this->once())->method('getFrontController')
            ->will($this->returnValue($this->_frontController));
        $this->_frontController->expects($this->any())->method('setAction');
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface');
        $this->_contextMock->expects($this->any())->method('getEventManager')
            ->will($this->returnValue($this->_eventManagerMock));
        $this->_eventManagerMock->expects($this->at(0))->method('dispatch')
            ->with('controller_action_layout_render_before');
        $this->_eventManagerMock->expects($this->at(1))->method('dispatch')
            ->with('controller_action_layout_render_before___');
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager',
            array('get', 'create', 'configure'), array(), '', false);
        $this->_contextMock->expects($this->any())->method('getObjectManager')
            ->will($this->returnValue($this->_objectManagerMock));
        $this->_layoutMock = $this->getMock('Magento\Core\Model\Layout\Factory',
            array('setArea', 'setDirectOutput', 'getOutput'), array(), '', false);
        $this->_contextMock->expects($this->any())->method('getLayout')
            ->will($this->returnValue($this->_layoutMock));
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_objectManagerMock->expects($this->at(0))->method('get')
            ->with('Magento\Config\ScopeInterface')->will($this->returnValue($this->_configScopeMock));
        $this->_objectManagerMock->expects($this->at(1))->method('get')
            ->with('Magento\Config\ScopeInterface')->will($this->returnValue($this->_configScopeMock));
        $this->_configScopeMock->expects($this->any())->method('getCurrentScope')
            ->will($this->returnValue('areaCode'));
        $this->_layoutMock->expects($this->any())->method('setArea')->with('areaCode');
        $this->_translateMock = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);
        $this->_objectManagerMock->expects($this->at(2))->method('get')
            ->with('Magento\Core\Model\Translate')->will($this->returnValue($this->_translateMock));

        $this->_model = new \Magento\Core\Controller\Varien\Action($this->_contextMock);
    }

    public function testRenderLayoutGetRenderNeverCall()
    {
        $this->_frontController->expects($this->never())->method('getNoRender');
        $this->_model->renderLayout();
    }

}