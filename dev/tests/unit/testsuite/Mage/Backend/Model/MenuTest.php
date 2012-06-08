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

        $valid = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $valid->expects($this->once())->method('isDisabled')->will($this->returnValue(false));
        $valid->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($valid);

        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $items = array();
        foreach ($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }

    public function testMultipleIterationsWorkProperly()
    {
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->addChild($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $valid1 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $valid1->expects($this->exactly(2))->method('isDisabled')->will($this->returnValue(false));
        $valid1->expects($this->exactly(2))->method('isAllowed')->will($this->returnValue(true));
        $valid1->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));
        $this->_model->addChild($valid1);

        $valid2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $valid2->expects($this->exactly(2))->method('isDisabled')->will($this->returnValue(false));
        $valid2->expects($this->exactly(2))->method('isAllowed')->will($this->returnValue(true));
        $valid2->expects($this->exactly(2))->method('getId')->will($this->returnValue(2));
        $this->_model->addChild($valid2);

        $items = array();
        foreach ($this->_model as $item) {
            $items[] = $item->getId();
        }

        $items2 = array();
        foreach ($this->_model as $item) {
            $items2[] = $item->getId();
        }
        $this->assertEquals($items, $items2);
    }

    public function testisChildLast()
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

        $this->assertTrue($this->_model->isChildLast($item2));
        $this->assertFalse($this->_model->isChildLast($item3));
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

    public function testgetChildById()
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
        $this->assertEquals($item, $this->_model->getChildById('item1'));
        $this->assertEquals($item2, $this->_model->getChildById('item2'));
    }

    public function testGetChildByIdRecursive()
    {
        $menuItem1 = new Mage_Backend_Model_Menu();
        $menuItem2 = new Mage_Backend_Model_Menu();

        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $item->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $item->expects($this->any())->method('getChildren')->will($this->returnValue($menuItem1));
        $this->_model->addChild($item);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->any())->method('getId')->will($this->returnValue('item2'));
        $item2->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item2->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $item2->expects($this->any())->method('getChildren')->will($this->returnValue($menuItem2));
        $menuItem1->addChild($item2);


        $item3 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item3->expects($this->any())->method('getId')->will($this->returnValue('item3'));
        $item3->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item3->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item3->expects($this->any())->method('hasChildren')->will($this->returnValue(false));
        $menuItem2->addChild($item3);

        $this->assertEquals($item, $this->_model->getChildById('item1', true));
        $this->assertEquals($item2, $this->_model->getChildById('item2', true));
        $this->assertEquals($item2, $this->_model->getChildById('item3', true));

        $this->assertEquals($item, $this->_model->getChildById('item1'));
        $this->assertNull($this->_model->getChildById('item2'));
        $this->assertNull($this->_model->getChildById('item3'));
    }

    /**
     * Test reset iterator to first element before each foreach
     */
    public function testNestedLoop()
    {
        $item1 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item1->expects($this->exactly(4))->method('getId')->will($this->returnValue('item1'));
        $item1->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item1->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item1);
        
        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->exactly(4))->method('getId')->will($this->returnValue('item2'));
        $item2->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item2);

        $item3 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item3->expects($this->exactly(4))->method('getId')->will($this->returnValue('item3'));
        $item3->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item3->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item3);

        $expected = array(
            'item1' => array('item1', 'item2', 'item3'),
            'item2' => array('item1', 'item2', 'item3'),
            'item3' => array('item1', 'item2', 'item3'),
        );
        $actual = array();
        foreach ($this->_model as $valLoop1) {
            $keyLevel1 = $valLoop1->getId();
            foreach ($this->_model as $valLoop2) {
                $actual[$keyLevel1][] = $valLoop2->getId();
            }
        }
        $this->assertEquals($expected, $actual);
    }

    public function testRemoveChildByIdRemovesMenuItem()
    {
        $item1 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item1->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $item1->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item1->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_model->addChild($item1);

        $this->assertCount(1, $this->_model);
        $this->assertEquals($item1, $this->_model->getChildById('item1'));

        $this->_model->removeChildById('item1');
        $this->assertCount(0, $this->_model);
        $this->assertNull($this->_model->getChildById('item1'));
    }

    public function testRemoveChildByIdRemovesMenuItemRecursively()
    {
        $menuMock = $this->getMock('Mage_Backend_Model_Menu');
        $menuMock->expects($this->once())
            ->method('removeChildById')
            ->with($this->equalTo('item2'));

        $item1 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item1->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $item1->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item1->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item1->expects($this->any())->method('hasChildren')->will($this->returnValue($menuMock));
        $item1->expects($this->any())->method('getChildren')->will($this->returnValue($menuMock));
        $this->_model->addChild($item1);

        $this->_model->removeChildById('item2');
    }

    public function testMoveChildById()
    {
        $item1 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item1->expects($this->once())->method('getId')->will($this->returnValue('item1'));
        $item1->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item1->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item1->expects($this->any())->method('hasSortIndex')->will($this->returnValue(true));
        $item1->expects($this->any())->method('getSortIndex')->will($this->returnValue(10));
        $this->_model->addChild($item1);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->once())->method('getId')->will($this->returnValue('item2'));
        $item2->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item2->expects($this->any())->method('hasSortIndex')->will($this->returnValue(true));
        $item2->expects($this->any())->method('getSortIndex')->will($this->returnValue(20));
        $this->_model->addChild($item2);

        $this->assertEquals($item2, $this->_model[20]);
        $this->_model->moveChildById('item2', 5);
        $this->assertEquals($item2, $this->_model[5]);
        $this->assertFalse(isset($this->_model[20]));
    }
}
