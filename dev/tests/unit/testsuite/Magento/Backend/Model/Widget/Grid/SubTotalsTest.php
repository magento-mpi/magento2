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

class Magento_Backend_Model_Widget_Grid_SubTotalsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Magento_Backend_Model_Widget_Grid_SubTotals
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
        $this->_parserMock = $this->getMock(
            'Magento_Backend_Model_Widget_Grid_Parser', array(), array(), '', false, false, false
        );

        $this->_factoryMock = $this->getMock(
            'Magento_Object_Factory', array('create'), array(), '', false, false, false
        );
        $this->_factoryMock->expects($this->any())
            ->method('create')
            ->with(array('sub_test1' => 3, 'sub_test2' => 2))
            ->will(
                $this->returnValue(
                    new Magento_Object(array('sub_test1' => 3, 'sub_test2' => 2))
                )
            );

        $arguments = array(
            'factory' => $this->_factoryMock,
            'parser' =>  $this->_parserMock
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_Backend_Model_Widget_Grid_SubTotals', $arguments);

        // setup columns
        $columns = array(
            'sub_test1' => 'sum',
            'sub_test2' => 'avg',
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
        $expected = new Magento_Object(
            array('sub_test1' => 3, 'sub_test2' => 2)
        );
        $this->assertEquals($expected, $this->_model->countTotals($this->_getTestCollection()));
    }

    /**
     * Retrieve test collection
     *
     * @return Magento_Data_Collection
     */
    protected function _getTestCollection()
    {
        $collection = new Magento_Data_Collection();
        $items = array(
            new Magento_Object(array('sub_test1' => '1', 'sub_test2' => '2')),
            new Magento_Object(array('sub_test1' => '1', 'sub_test2' => '2')),
            new Magento_Object(array('sub_test1' => '1', 'sub_test2' => '2'))
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }

        return $collection;
    }
}
