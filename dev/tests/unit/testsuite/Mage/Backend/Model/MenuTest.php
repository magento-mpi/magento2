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
    protected $_model;

    /**
     * @var Mage_Backend_Model_Menu_Item[]
     */
    protected $_items = array();

    public function setUp()
    {
        $this->_items['item1'] = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_items['item1']->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $this->_items['item1']->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $this->_items['item1']->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $this->_items['item2'] = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_items['item2']->expects($this->any())->method('getId')->will($this->returnValue('item2'));
        $this->_items['item2']->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $this->_items['item2']->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $this->_items['item3'] = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_items['item3']->expects($this->any())->method('getId')->will($this->returnValue('item3'));
        $this->_items['item3']->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $this->_items['item3']->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $this->_model = new Mage_Backend_Model_Menu();
    }

    public function testAdd()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_model->add($item);
        $this->assertCount(1, $this->_model);
        $this->assertEquals($item, $this->_model[0]);
    }

    public function testAddToItem()
    {
        $subMenu = $this->getMock("Mage_Backend_Model_Menu");
        $subMenu->expects($this->once())
            ->method("add")
            ->with($this->_items['item2']);

        $this->_items['item1']->expects($this->once())
            ->method("getChildren")
            ->will($this->returnValue($subMenu));

        $this->_model->add($this->_items['item1']);
        $this->_model->add($this->_items['item2'], 'item1');
    }

    public function testAddWithSortIndexThatAlreadyExistsAddsItemOnNextAvailableIndex()
    {
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_model->add($this->_items['item1'], null, 2);
        $this->assertCount(4, $this->_model);
        $this->assertEquals($this->_items['item1'], $this->_model[3]);
    }

    public function testAddSortsItemsByTheirSortIndex()
    {
        $this->_model->add($this->_items['item1'], null, 10);
        $this->_model->add($this->_items['item2'], null, 20);
        $this->_model->add($this->_items['item3'], null, 15);

        $this->assertCount(3, $this->_model);
        $itemsOrdered = array();
        foreach ($this->_model as $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            $itemsOrdered[] = $item->getId();
        }
        $this->assertEquals(array('item1', 'item3', 'item2'), $itemsOrdered);
    }

    public function testGet()
    {
        $this->_model->add($this->_items['item1']);
        $this->_model->add($this->_items['item2']);

        $this->assertEquals($this->_items['item1'], $this->_model[0]);
        $this->assertEquals($this->_items['item2'], $this->_model[1]);
        $this->assertEquals($this->_items['item1'], $this->_model->get('item1'));
        $this->assertEquals($this->_items['item2'], $this->_model->get('item2'));
    }

    public function testGetRecursive()
    {
        $menu1 = new Mage_Backend_Model_Menu();
        $menu2 = new Mage_Backend_Model_Menu();

        $this->_items['item1']->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $this->_items['item1']->expects($this->any())->method('getChildren')->will($this->returnValue($menu1));
        $this->_model->add($this->_items['item1']);

        $this->_items['item2']->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $this->_items['item2']->expects($this->any())->method('getChildren')->will($this->returnValue($menu2));
        $menu1->add($this->_items['item2']);

        $this->_items['item3']->expects($this->any())->method('hasChildren')->will($this->returnValue(false));
        $menu2->add($this->_items['item3']);

        $this->assertEquals($this->_items['item1'], $this->_model->get('item1'));
        $this->assertEquals($this->_items['item2'], $this->_model->get('item2'));
        $this->assertEquals($this->_items['item3'], $this->_model->get('item3'));
    }

    public function testMoveAddsItemToNewItem()
    {
        $this->markTestIncomplete();
    }

    public function testMoveNonExistentItemThrowsException()
    {
        $this->markTestIncomplete();
    }

    public function testMoveToNonExistentItemThrowsException()
    {
        $this->markTestIncomplete();
    }

    public function testRemoveRemovesMenuItem()
    {
        $this->_model->add($this->_items['item1']);

        $this->assertCount(1, $this->_model);
        $this->assertEquals($this->_items['item1'], $this->_model->get('item1'));

        $this->_model->remove('item1');
        $this->assertCount(0, $this->_model);
        $this->assertNull($this->_model->get('item1'));
    }

    public function testRemoveRemovesMenuItemRecursively()
    {
        $menuMock = $this->getMock('Mage_Backend_Model_Menu');
        $menuMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo('item2'));

        $this->_items['item1']->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $this->_items['item1']->expects($this->any())->method('getChildren')->will($this->returnValue($menuMock));
        $this->_model->add($this->_items['item1']);

        $this->_model->remove('item2');
    }

    public function testReorderReordersItemOnTopLevel()
    {
        $this->_model->add($this->_items['item1'], null, 10);
        $this->_model->add($this->_items['item2'], null, 20);

        $this->assertEquals($this->_items['item2'], $this->_model[20]);
        $this->_model->reorder('item2', 5);
        $this->assertEquals($this->_items['item2'], $this->_model[5]);
        $this->assertFalse(isset($this->_model[20]));
    }

    public function testReorderReordersItemOnItsLevel()
    {
        $this->markTestIncomplete();
    }

    public function testIsLast()
    {
        $this->_model->add($this->_items['item1'], null, 10);
        $this->_model->add($this->_items['item2'], null, 16);
        $this->_model->add($this->_items['item3'], null, 15);

        $this->assertTrue($this->_model->isLast($this->_items['item2']));
        $this->assertFalse($this->_model->isLast($this->_items['item3']));
    }

    public function testSetPathUpdatesAllChildren()
    {
        $this->_items['item1']->expects($this->exactly(2))->method('setPath');
        $this->_model->add($this->_items['item1']);

        $this->_items['item2']->expects($this->exactly(2))->method('setPath');
        $this->_model->add($this->_items['item2']);

        $this->_model->setpath('root');
    }

    public function testGetFirstAvailableReturnsLeafNode()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item->expects($this->once())->method('setPath');
        $item->expects($this->exactly(1))->method('isDisabled')->will($this->returnValue(true));
        $item->expects($this->never())->method('getFirstAvailable');
        $this->_model->add($item);

        $this->_items['item1']->expects($this->once())->method('hasChildren');
        $this->_items['item1']->expects($this->once())->method('getAction')
            ->will($this->returnValue('/root/system/node'));
        $this->_model->add($this->_items['item1']);

        $this->assertEquals('/root/system/node', $this->_model->getFirstAvailable()->getAction());
    }

    public function testNextWithAllItemsDisabledDoesntIterate()
    {
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $items = array();
        foreach ($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertCount(0, $items);
    }

    public function testNextIteratesOnlyValidItems()
    {
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_model->add($this->_items['item1']);

        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $items = array();
        foreach ($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }

    public function testMultipleIterationsWorkProperly()
    {
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_model->add($this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_model->add($this->_items['item1']);
        $this->_model->add($this->_items['item2']);

        $items = array();
        /** @var $item Mage_Backend_Model_Menu_Item */
        foreach ($this->_model as $item) {
            $items[] = $item->getId();
        }

        $items2 = array();
        foreach ($this->_model as $item) {
            $items2[] = $item->getId();
        }
        $this->assertEquals($items, $items2);
    }

    /**
     * Test reset iterator to first element before each foreach
     */
    public function testNestedLoop()
    {
        $this->_model->add($this->_items['item1']);
        $this->_model->add($this->_items['item2']);
        $this->_model->add($this->_items['item3']);

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
}
