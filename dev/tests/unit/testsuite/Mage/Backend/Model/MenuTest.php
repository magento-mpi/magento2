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
        $item->expects($this->once())->method('hasSortIndex')->will($this->returnValue(true));
        $item->expects($this->exactly(2))->method('getSortIndex')->will($this->returnValue(10));
        $item->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item);


        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->once())->method('hasSortIndex')->will($this->returnValue(true));
        $item2->expects($this->exactly(2))->method('getSortIndex')->will($this->returnValue(20));
        $item2->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item2);

        $item3 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item3->expects($this->once())->method('hasSortIndex')->will($this->returnValue(true));
        $item3->expects($this->exactly(2))->method('getSortIndex')->will($this->returnValue(15));
        $item3->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item3->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item3);

        $this->assertCount(3, $this->_model);
        $itemsOrdered = array();
        foreach ($this->_model as $item) {
            $itemsOrdered[] = $item->getSortIndex();
        }
        $this->assertEquals(array(10, 15, 20), $itemsOrdered);
    }

    public function testNextWithAllItemsDisabledDoesntIterate()
    {
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $items = array();
        foreach ($this->_model as $item) {
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
        foreach ($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }

    public function testIsLast()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->any())->method('getId')->will($this->returnValue(1));
        $item->expects($this->once())->method('hasSortIndex')->will($this->returnValue(true));
        $item->expects($this->once())->method('getSortIndex')->will($this->returnValue(10));
        $this->_model->addChild($item);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->any())->method('getId')->will($this->returnValue(2));
        $item2->expects($this->once())->method('hasSortIndex')->will($this->returnValue(true));
        $item2->expects($this->once())->method('getSortIndex')->will($this->returnValue(16));
        $this->_model->addChild($item2);

        $item3 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item3->expects($this->any())->method('getId')->will($this->returnValue(3));
        $item3->expects($this->once())->method('hasSortIndex')->will($this->returnValue(true));
        $item3->expects($this->once())->method('getSortIndex')->will($this->returnValue(15));
        $this->_model->addChild($item3);

        $this->assertTrue($this->_model->isLast($item2));
        $this->assertFalse($this->_model->isLast($item3));
    }

    public function testSetPathUpdatesAllChildren()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->exactly(2))->method('setParent');
        $item->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->exactly(2))->method('setParent');
        $item2->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item2);

        $this->_model->setpath('root');
    }

    public function testGetFirstAvailableChildReturnsLeafNode()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->once())->method('setParent');
        $item->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item->expects($this->once())->method('isAllowed')->will($this->returnValue(false));
        $item->expects($this->never())->method('getFirstAvailableChild');
        $this->_model->addChild($item);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->once())->method('setParent');
        $item2->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $item2->expects($this->once())->method('getFirstAvailableChild')->will($this->returnValue('/root/system/node'));
        $this->_model->addChild($item2);

        $this->assertEquals('/root/system/node', $this->_model->getFirstAvailableChild());
    }

    public function testOffsetGetReturnsItemById()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->exactly(2))->method('getId')->will($this->returnValue('item1'));
        $item->expects($this->exactly(2))->method('isDisabled')->will($this->returnValue(false));
        $item->expects($this->exactly(2))->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->once())->method('getId')->will($this->returnValue('item2'));
        $item2->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item2);

        $this->assertEquals($item, $this->_model[0]);
        $this->assertEquals($item2, $this->_model[1]);
        $this->assertEquals($item, $this->_model->getById('item1'));
        $this->assertEquals($item2, $this->_model->getById('item2'));
    }
}
