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


class Mage_Backend_Model_Menu_BuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mage_Backend_Model_Menu_Builder
     */
    protected  $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuMock;

    public function setUp()
    {
        $that = $this;
        $factory = $this->getMock("Mage_Backend_Model_Menu_Item_Factory");
        $factory->expects($this->any())
            ->method('createFromArray')
            ->will(
            $this->returnCallback(function($params) use ($that) {
                return $that->getMock('Mage_Backend_Model_Menu_Item', array(), $params, '', false);
            })
        );
        $this->_menuMock = $this->getMock('Mage_Backend_Model_Menu');

        $this->_model = new Mage_Backend_Model_Menu_Builder(array(
            'itemFactory' => $factory,
            'menu' => $this->_menuMock
        ));
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

    public function testGetResult()
    {
        $this->_model->processCommand(new Mage_Backend_Model_Menu_Builder_Command_Add(array('id' => 1)));
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(array('id' => 2, 'parent' => 1))
        );
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(array('id' => 3, 'parent' => 2))
        );

        $this->_menuMock->expects($this->exactly(1))
            ->method('addChild')
            ->with($this->isInstanceOf('Mage_Backend_Model_Menu_Item'));

        $this->_model->getResult();
    }

}
