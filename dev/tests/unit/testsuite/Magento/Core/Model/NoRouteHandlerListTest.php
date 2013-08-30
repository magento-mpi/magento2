<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_NoRouteHandlerListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var Mage_Core_Model_NoRouteHandlerList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $handlersList = array(
            'default_handler' => array(
                'instance' => 'Mage_Core_Model_Router_NoRouteHandler',
                'sortOrder' => 100
            ),
            'backend_handler' => array(
                'instance'  => 'Mage_Backend_Model_Router_NoRouteHandler',
                'sortOrder' => 10
            ),
        );

        $this->_model = new Mage_Core_Model_NoRouteHandlerList($this->_objectManagerMock, $handlersList);
    }

    public function testGetHandlers()
    {
        $backendHandlerMock = $this->getMock('Mage_Backend_Model_Router_NoRouteHandler', array(), array(), '', false);
        $defaultHandlerMock = $this->getMock('Mage_Core_Model_Router_NoRouteHandler', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('Mage_Backend_Model_Router_NoRouteHandler')
            ->will($this->returnValue($backendHandlerMock));

        $this->_objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with('Mage_Core_Model_Router_NoRouteHandler')
            ->will($this->returnValue($defaultHandlerMock));


        $expectedResult = array(
            '0' => $backendHandlerMock,
            '1' => $defaultHandlerMock
        );

        $this->assertEquals($expectedResult, $this->_model->getHandlers());
    }
}
