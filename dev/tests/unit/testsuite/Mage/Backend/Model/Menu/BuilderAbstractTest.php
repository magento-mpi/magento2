<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Menu_BuilderAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_BuilderAbstract
     */
    protected  $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_Backend_Model_Menu_BuilderAbstract');
    }

    public function testProcessCommand()
    {
        $command = $this->getMock(
            'Mage_Backend_Model_Menu_Builder_Command_Add', array('chain'), array(array('id' => 1))
        );
        $command2 = $this->getMock(
            'Mage_Backend_Model_Menu_Builder_Command_Update', array('chain'), array(array('id' => 1))
        );
        $command->expects($this->once())
            ->method('chain')
            ->with($this->equalTo($command2));
        $this->_model->processCommand($command);
        $this->_model->processCommand($command2);
    }
}
