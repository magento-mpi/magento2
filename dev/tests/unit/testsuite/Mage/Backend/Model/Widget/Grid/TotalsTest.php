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

class Mage_Backend_Model_Widget_Grid_TotalsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Mage_Backend_Model_Widget_Grid_Totals
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parserMock;

    protected function setUp()
    {
        // prepare model
        $this->_parserMock = $this->getMock(
            'Mage_Backend_Model_Widget_Grid_Parser', array('parseExpression'), array(), '', false, false, false
        );
        $this->_parserMock->expects($this->any())
            ->method('parseExpression')
            ->with('test1+test2')
            ->will($this->returnValue(array('test1', 'test2', '+')));
        $this->_model = new Mage_Backend_Model_Widget_Grid_Totals($this->_parserMock);

        // setup columns
        $columns = array(
            'test1' => 'sum',
            'test2' => 'avg',
            'test3' => 'test1+test2'
        );
        foreach ($columns as $index => $expression) {
            $this->_model->setColumn($index, $expression);
        }
    }

    protected function tearDown()
    {
        unset($this->_parserMock);
    }

    public function testCountTotals()
    {
        // prepare collection
        $collection = new Varien_Data_Collection();
        $items = array(
            new Varien_Object(array('test1' => '1', 'test2' => '2')),
            new Varien_Object(array('test1' => '1', 'test2' => '2')),
            new Varien_Object(array('test1' => '1', 'test2' => '2'))
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }

        $expected = new Varien_Object(array('test1' => 3, 'test2' => 2, 'test3' => 5));
        $this->assertEquals($expected, $this->_model->countTotals($collection));
    }

    public function testCountTotalsWithSubItems()
    {
        $this->_model->reset(true);
        $this->_model->setColumn('test4', 'sum');
        $this->_model->setColumn('test5', 'avg');

        // prepare collection
        $collection = new Varien_Data_Collection();
        $items = array(
            new Varien_Object(array('children' => new Varien_Object(array('test4' => '1','test5' => '2')))),
            new Varien_Object(array('children' => new Varien_Object(array('test4' => '1','test5' => '2')))),
            new Varien_Object(array('children' => new Varien_Object(array('test4' => '1','test5' => '2')))),
        );
        foreach ($items as $item) {
            // prepare sub-collection
            $subCollection = new Varien_Data_Collection();
            $subCollection->addItem(new Varien_Object(array('test4' => '1','test5' => '2')));
            $subCollection->addItem(new Varien_Object(array('test4' => '2','test5' => '2')));
            $item->setChildren($subCollection);
            $collection->addItem($item);
        }
        $expected = new Varien_Object(array('test4' => 9, 'test5' => 2));
        $this->assertEquals($expected, $this->_model->countTotals($collection));
    }
}
