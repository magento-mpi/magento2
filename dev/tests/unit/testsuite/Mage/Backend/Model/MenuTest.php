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

class Mage_Backend_Model_MenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu
     */
    protected  $_model;

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Menu();
    }

    public function testAddChild()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_model->addChild($item);
        $this->assertCount(1, $this->_model);
        $this->assertEquals($item, $this->_model[0]);
    }

    public function testAddChildWithSortIndexThatAlreadyExistsAddsItemOnNextAvailableIndex()
    {
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->once())
            ->method('hasSortIndex')->will($this->returnValue(true));
        $item->expects($this->once())
            ->method('getSortIndex')->will($this->returnValue(2));

        $this->_model->addChild($item);
        $this->assertCount(4, $this->_model);
        $this->assertEquals($item, $this->_model[3]);
    }

    public function testAddChildSortsItemsByTheirSortIndex()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->once())
            ->method('hasSortIndex')->will($this->returnValue(true));
        $item->expects($this->once())
            ->method('getSortIndex')->will($this->returnValue(10));
        $this->_model->addChild($item);


        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->once())
            ->method('hasSortIndex')->will($this->returnValue(true));
        $item2->expects($this->once())
            ->method('getSortIndex')->will($this->returnValue(20));
        $this->_model->addChild($item2);

        $item3 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item3->expects($this->once())
            ->method('hasSortIndex')->will($this->returnValue(true));
        $item3->expects($this->once())
            ->method('getSortIndex')->will($this->returnValue(15));
        $this->_model->addChild($item3);

        $this->assertCount(3, $this->_model);
        $this->assertEquals($item3, $this->_model[15]);
    }

    public function testNextWithAllItemsDisabledDoesntIterate()
    {
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $items = array();
        foreach($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertCount(0, $items);
    }

    public function testNextIteratesOnlyValidItems()
    {
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->once())
            ->method('isDisabled')
            ->will($this->returnValue(false));

        $item->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));
        $this->_model->addChild($item);
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $items = array();
        foreach($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }
}
