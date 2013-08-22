<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Layout_Argument_Updater
 */
class Magento_Core_Model_Layout_Argument_UpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Updater
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_argUpdaterMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_argUpdaterMock = $this->getMock('Magento_Core_Model_Layout_Argument_UpdaterInterface', array(), array(),
            '', false
        );

        $this->_model = new Magento_Core_Model_Layout_Argument_Updater($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_argUpdaterMock);
        unset($this->_objectManagerMock);
    }

    public function testApplyUpdatersWithValidUpdaters()
    {
        $value = 1;

        $this->_objectManagerMock->expects($this->exactly(2))
            ->method('create')
            ->with($this->logicalOr('Dummy_Updater_1', 'Dummy_Updater_2'))
            ->will($this->returnValue($this->_argUpdaterMock));

        $this->_argUpdaterMock->expects($this->exactly(2))
            ->method('update')
            ->with($value)
            ->will($this->returnValue($value));

        $updaters = array('Dummy_Updater_1', 'Dummy_Updater_2');
        $this->assertEquals($value, $this->_model->applyUpdaters($value, $updaters));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testApplyUpdatersWithInvalidUpdaters()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Dummy_Updater_1')
            ->will($this->returnValue(new StdClass()));
        $updaters = array('Dummy_Updater_1', 'Dummy_Updater_2');

        $this->_model->applyUpdaters(1, $updaters);
    }
}
