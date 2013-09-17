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
     * @var Magento_ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var Magento_Core_Model_NoRouteHandlerList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $handlersList = array(
            'default_handler' => array(
                'instance' => 'Magento_Core_Model_Router_NoRouteHandler',
                'sortOrder' => 100
            ),
            'backend_handler' => array(
                'instance'  => 'Magento_Backend_Model_Router_NoRouteHandler',
                'sortOrder' => 10
            ),
        );

        $this->_model = new Magento_Core_Model_NoRouteHandlerList($this->_objectManagerMock, $handlersList);
    }

    public function testGetHandlers()
    {
        $backendHandlerMock = $this->getMock(
            'Magento_Backend_Model_Router_NoRouteHandler', array(), array(), '', false
        );
        $defaultHandlerMock = $this->getMock('Magento_Core_Model_Router_NoRouteHandler', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('Magento_Backend_Model_Router_NoRouteHandler')
            ->will($this->returnValue($backendHandlerMock));

        $this->_objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with('Magento_Core_Model_Router_NoRouteHandler')
            ->will($this->returnValue($defaultHandlerMock));


        $expectedResult = array(
            '0' => $backendHandlerMock,
            '1' => $defaultHandlerMock
        );

        $this->assertEquals($expectedResult, $this->_model->getHandlers());
    }
}
