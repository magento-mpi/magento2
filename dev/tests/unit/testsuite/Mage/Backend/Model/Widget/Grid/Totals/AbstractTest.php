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

class Mage_Backend_Model_Widget_Grid_Totals_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Mage_Backend_Model_Widget_Grid_Totals_AbstractImplementation
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
        $this->_model = new Mage_Backend_Model_Widget_Grid_Totals_AbstractImplementation($this->_parserMock);

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

    public function testColumns()
    {
        $expected = array(
            'test1' => 'sum',
            'test2' => 'avg',
            'test3' => 'test1+test2'
        );

        $this->assertEquals($expected, $this->_model->getColumns());
    }

    public function testCountTotals()
    {
        $this->_parserMock->expects($this->any())
            ->method('parseExpression')
            ->with('test1+test2')
            ->will($this->returnValue(array('test1', 'test2', '+')));

        // prepare collection
        $collection = new Varien_Data_Collection();
        $items = array(
            new Varien_Object(array('test1' => '1', 'test2' => '2')),
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }

        $expected = new Varien_Object(array('test1' => 0, 'test2' => 0, 'test3' => 0));
        $this->assertEquals($expected, $this->_model->countTotals($collection));
    }

    public function testReset()
    {
        $this->_parserMock->expects($this->any())
            ->method('parseExpression')
            ->with('test1+test2')
            ->will($this->returnValue(array('test1', 'test2', '+')));

        // prepare collection
        $collection = new Varien_Data_Collection();
        $items = array(
            new Varien_Object(array('test1' => '1', 'test2' => '2')),
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }
        $this->_model->countTotals($collection);
        $this->_model->reset();

        $this->assertEquals(new Varien_Object(), $this->_model->getTotals());
        $this->assertNotEmpty($this->_model->getColumns());
    }

    public function testResetFull()
    {
        $this->_parserMock->expects($this->any())
            ->method('parseExpression')
            ->with('test1+test2')
            ->will($this->returnValue(array('test1', 'test2', '+')));

        // prepare collection
        $collection = new Varien_Data_Collection();
        $items = array(
            new Varien_Object(array('test1' => '1', 'test2' => '2')),
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }
        $this->_model->countTotals($collection);
        $this->_model->reset(true);

        $this->assertEquals(new Varien_Object(), $this->_model->getTotals());
        $this->assertEmpty($this->_model->getColumns());
    }
}

class Mage_Backend_Model_Widget_Grid_Totals_AbstractImplementation
    extends Mage_Backend_Model_Widget_Grid_Totals_Abstract
{

    /**
     * Count collection column sum based on column index
     *
     * @param $index
     * @param $collection
     * @return float|int
     */
    protected function _countSum($index, $collection)
    {
        return 0;
    }

    /**
     * Count collection column average based on column index
     *
     * @param $index
     * @param $collection
     * @return float|int
     */
    protected function _countAverage($index, $collection)
    {
        return 0;
    }
}