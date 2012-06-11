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

    /**
     * @var
     */
    protected $_factoryMock;

    public function setUp()
    {
        $that = $this;
        $this->_factoryMock = $this->getMock("Mage_Backend_Model_Menu_Item_Factory", array(), array(), '', false);
        $this->_factoryMock->expects($this->any())
            ->method('createFromArray')
            ->will(
                $this->returnCallback(
                    function($params) use ($that) {
                        return $that->getMock('Mage_Backend_Model_Menu_Item', array(), $params, '', false);
                    }
                )
            );
        $this->_menuMock = $this->getMock('Mage_Backend_Model_Menu');

        $this->_model = new Mage_Backend_Model_Menu_Builder(array(
            'itemFactory' => $this->_factoryMock,
            'menu' => $this->_menuMock
        ));
    }

    public function testProcessCommand()
    {
        $command = $this->getMock('Mage_Backend_Model_Menu_Builder_Command_Add', array(), array(), '', false);
        $command->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $command2 = $this->getMock('Mage_Backend_Model_Menu_Builder_Command_Update', array(), array(), '', false);
        $command2->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $command->expects($this->once())
            ->method('chain')
            ->with($this->equalTo($command2));
        $this->_model->processCommand($command);
        $this->_model->processCommand($command2);
    }

    public function testGetResult()
    {
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(
                array('id' => 'item1', 'title' => 'Item 1', 'module' => 'Mage_Backend', 'sortOrder' => 2)
            )
        );
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(
                array(
                    'id' => 'item2', 'parent' => 'item1', 'title' => 'two',
                    'module' => 'Mage_Backend', 'sortOrder' => 4
                )
            )
        );
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(
                array(
                    'id' => 'item3', 'parent' => 'item2', 'title' => 'three',
                    'module' => 'Mage_Backend', 'sortOrder' => 6
                )
            )
        );

        $this->_menuMock->expects($this->at(0))
            ->method('add')
            ->with(
                $this->isInstanceOf('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(null),
                $this->equalTo(2)
            );

        $this->_menuMock->expects($this->at(1))
            ->method('add')
            ->with(
                $this->isInstanceOf('Mage_Backend_Model_Menu_Item'),
                $this->equalTo('item1'),
                $this->equalTo(4)
            );

        $this->_menuMock->expects($this->at(2))
            ->method('add')
            ->with(
                $this->isInstanceOf('Mage_Backend_Model_Menu_Item'),
                $this->equalTo('item2'),
                $this->equalTo(6)
            );

        $this->_model->getResult();
    }

    public function testGetResultSkipsRemovedItems()
    {
        $this->_model->processCommand(new Mage_Backend_Model_Menu_Builder_Command_Add(array(
                'id' => 1,
                'title' => 'Item 1',
                'module' => 'Mage_Backend'
            )
        ));
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Remove(
                array('id' => 1,)
            )
        );

        $this->_menuMock->expects($this->never())
            ->method('addChild');

        $this->_model->getResult();
    }
}
