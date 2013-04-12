<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Queue_DefaultHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    /**
     * @var Enterprise_Queue_Model_Queue_DefaultHandler
     */
    protected $_defaultHandler;

    protected function setUp()
    {
        $this->_adapterMock = $this->getMock('Enterprise_Queue_Model_Queue_AdapterInterface');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_defaultHandler = $objectManagerHelper->getObject('Enterprise_Queue_Model_Queue_DefaultHandler', array(
            'adapter' => $this->_adapterMock,
        ));
    }

    public function testAddTaskTest()
    {
        $this->_adapterMock->expects($this->once())->method('addTask')->with('some_event', array('123'), 7)
            ->will($this->returnSelf());

        $this->assertEquals($this->_defaultHandler, $this->_defaultHandler->addTask('some_event', array('123'), 7));
    }
}
