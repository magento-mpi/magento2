<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Queue_Model_Queue_DefaultHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $arguments
     * @return Enterprise_Queue_Model_Queue_DefaultHandler
     */
    protected function _getQueueDefaultHandler($arguments = array())
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        return $objectManagerHelper->getObject('Enterprise_Queue_Model_Queue_DefaultHandler', $arguments);
    }

    public function testAddTaskTest()
    {
        $adapterMock = $this->getMock('Enterprise_Queue_Model_Queue_Adapter_AdapterInterface', array(), array(), '',
            false);
        $adapterMock->expects($this->once())->method('addTask')->with('some_event', array('123'), 7)
            ->will($this->returnSelf());

        $defaultHandler = $this->_getQueueDefaultHandler(array(
            'adapter' => $adapterMock
        ));
        $this->assertEquals($defaultHandler, $defaultHandler->addTask('some_event', array('123'), 7));
    }
}
