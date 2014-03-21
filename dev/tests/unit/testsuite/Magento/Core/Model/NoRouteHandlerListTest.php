<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class NoRouteHandlerListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\App\Router\NoRouteHandlerList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $handlersList = array(
            'default_handler' => array('instance' => 'Magento\Core\App\Router\NoRouteHandler', 'sortOrder' => 100),
            'backend_handler' => array('instance' => 'Magento\Backend\App\Router\NoRouteHandler', 'sortOrder' => 10)
        );

        $this->_model = new \Magento\App\Router\NoRouteHandlerList($this->_objectManagerMock, $handlersList);
    }

    public function testGetHandlers()
    {
        $backendHandlerMock = $this->getMock('Magento\Backend\App\Router\NoRouteHandler', array(), array(), '', false);
        $defaultHandlerMock = $this->getMock('Magento\Core\App\Router\NoRouteHandler', array(), array(), '', false);

        $this->_objectManagerMock->expects(
            $this->at(0)
        )->method(
            'create'
        )->with(
            'Magento\Backend\App\Router\NoRouteHandler'
        )->will(
            $this->returnValue($backendHandlerMock)
        );

        $this->_objectManagerMock->expects(
            $this->at(1)
        )->method(
            'create'
        )->with(
            'Magento\Core\App\Router\NoRouteHandler'
        )->will(
            $this->returnValue($defaultHandlerMock)
        );


        $expectedResult = array('0' => $backendHandlerMock, '1' => $defaultHandlerMock);

        $this->assertEquals($expectedResult, $this->_model->getHandlers());
    }
}
