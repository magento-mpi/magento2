<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Menu_Filter_IteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Menu
     */
    protected $_menuModel;

    /**
     * @var Magento_Backend_Model_Menu_Filter_Iterator
     */
    protected $_filterIteratorModel;

    /**
     * @var Magento_Backend_Model_Menu_Item[]
     */
    protected $_items = array();

    protected function setUp()
    {
        $this->_items['item1'] = $this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_items['item1']->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $this->_items['item1']->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $this->_items['item1']->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $this->_items['item2'] = $this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_items['item2']->expects($this->any())->method('getId')->will($this->returnValue('item2'));
        $this->_items['item2']->expects($this->any())->method('isDisabled')->will($this->returnValue(true));
        $this->_items['item2']->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $this->_items['item3'] = $this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false);
        $this->_items['item3']->expects($this->any())->method('getId')->will($this->returnValue('item3'));
        $this->_items['item3']->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $this->_items['item3']->expects($this->any())->method('isAllowed')->will($this->returnValue(false));

        $loggerMock = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);

        $this->_menuModel = new Magento_Backend_Model_Menu($loggerMock);
        $this->_filterIteratorModel = new Magento_Backend_Model_Menu_Filter_Iterator($this->_menuModel->getIterator());
    }


    public function testLoopWithAllItemsDisabledDoesntIterate()
    {
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $items = array();
        foreach ($this->_filterIteratorModel as $item) {
            $items[] = $item;
        }
        $this->assertCount(0, $items);
    }

    public function testLoopIteratesOnlyValidItems()
    {
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_menuModel->add($this->_items['item1']);

        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $items = array();
        foreach ($this->_filterIteratorModel as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }

    public function testLoopIteratesDosntIterateDisabledItems()
    {
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_menuModel->add($this->_items['item1']);
        $this->_menuModel->add($this->_items['item2']);

        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $items = array();
        foreach ($this->_filterIteratorModel as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }

    public function testLoopIteratesDosntIterateNotAllowedItems()
    {
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_menuModel->add($this->_items['item1']);
        $this->_menuModel->add($this->_items['item3']);

        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $items = array();
        foreach ($this->_filterIteratorModel as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }

    public function testLoopIteratesMixedItems()
    {
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $this->_menuModel->add($this->_items['item1']);
        $this->_menuModel->add($this->_items['item2']);
        $this->_menuModel->add($this->_items['item3']);

        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));
        $this->_menuModel->add($this->getMock('Magento_Backend_Model_Menu_Item', array(), array(), '', false));

        $items = array();
        foreach ($this->_filterIteratorModel as $item) {
            $items[] = $item;
        }
        $this->assertCount(1, $items);
    }
}
