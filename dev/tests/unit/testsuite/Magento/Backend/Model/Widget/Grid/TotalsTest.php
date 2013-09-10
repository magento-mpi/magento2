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

class Magento_Backend_Model_Widget_Grid_TotalsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Magento_Backend_Model_Widget_Grid_Totals
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parserMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        // prepare model
        $this->_parserMock = $this->getMock(
            'Magento_Backend_Model_Widget_Grid_Parser', array('parseExpression'), array(), '', false, false, false
        );

        $this->_factoryMock = $this->getMock(
            'Magento_Object_Factory', array('create'), array(), '', false, false, false
        );

        $createValueMap = array(
            array(
                array('test1' => 3, 'test2' => 2),
                new Magento_Object(array('test1' => 3, 'test2' => 2))
            ),
            array(
                array('test4' => 9, 'test5' => 2),
                new Magento_Object(array('test4' => 9, 'test5' => 2))
            )
        );
        $this->_factoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap($createValueMap));

        $arguments = array(
            'factory' => $this->_factoryMock,
            'parser' =>  $this->_parserMock
        );

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_Backend_Model_Widget_Grid_Totals', $arguments);

        // setup columns
        $columns = array(
            'test1' => 'sum',
            'test2' => 'avg'
        );
        foreach ($columns as $index => $expression) {
            $this->_model->setColumn($index, $expression);
        }
    }

    protected function tearDown()
    {
        unset($this->_parserMock);
        unset($this->_factoryMock);
    }

    public function testCountTotals()
    {
        // prepare collection
        $collection = new Magento_Data_Collection();
        $items = array(
            new Magento_Object(array('test1' => '1', 'test2' => '2')),
            new Magento_Object(array('test1' => '1', 'test2' => '2')),
            new Magento_Object(array('test1' => '1', 'test2' => '2'))
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }

        $expected = new Magento_Object(array('test1' => 3, 'test2' => 2));
        $this->assertEquals($expected, $this->_model->countTotals($collection));
    }

    public function testCountTotalsWithSubItems()
    {
        $this->_model->reset(true);
        $this->_model->setColumn('test4', 'sum');
        $this->_model->setColumn('test5', 'avg');

        // prepare collection
        $collection = new Magento_Data_Collection();
        $items = array(
            new Magento_Object(array('children' => new Magento_Object(array('test4' => '1','test5' => '2')))),
            new Magento_Object(array('children' => new Magento_Object(array('test4' => '1','test5' => '2')))),
            new Magento_Object(array('children' => new Magento_Object(array('test4' => '1','test5' => '2')))),
        );
        foreach ($items as $item) {
            // prepare sub-collection
            $subCollection = new Magento_Data_Collection();
            $subCollection->addItem(new Magento_Object(array('test4' => '1','test5' => '2')));
            $subCollection->addItem(new Magento_Object(array('test4' => '2','test5' => '2')));
            $item->setChildren($subCollection);
            $collection->addItem($item);
        }
        $expected = new Magento_Object(array('test4' => 9, 'test5' => 2));
        $this->assertEquals($expected, $this->_model->countTotals($collection));
    }
}
