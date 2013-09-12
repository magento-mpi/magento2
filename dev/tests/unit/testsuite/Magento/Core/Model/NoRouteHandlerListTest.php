<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_NoRouteHandlerListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Core\Model\NoRouteHandlerList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $handlersList = array(
            'default_handler' => array(
                'instance' => 'Magento\Core\Model\Router\NoRouteHandler',
                'sortOrder' => 100
            ),
            'backend_handler' => array(
                'instance'  => 'Magento\Backend\Model\Router\NoRouteHandler',
                'sortOrder' => 10
            ),
        );

        $this->_model = new \Magento\Core\Model\NoRouteHandlerList($this->_objectManagerMock, $handlersList);
    }

    public function testGetHandlers()
    {
        $backendHandlerMock = $this->getMock(
            'Magento\Backend\Model\Router\NoRouteHandler', array(), array(), '', false
        );
        $defaultHandlerMock = $this->getMock('Magento\Core\Model\Router\NoRouteHandler', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('Magento\Backend\Model\Router\NoRouteHandler')
            ->will($this->returnValue($backendHandlerMock));

        $this->_objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with('Magento\Core\Model\Router\NoRouteHandler')
            ->will($this->returnValue($defaultHandlerMock));


        $expectedResult = array(
            '0' => $backendHandlerMock,
            '1' => $defaultHandlerMock
        );

        $this->assertEquals($expectedResult, $this->_model->getHandlers());
    }
}
