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

class Magento_Backend_Model_Widget_Grid_TotalsAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model PHPUnit_Framework_MockObject_MockObject
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

    /**
     * Columns map for parserMock return expressions
     *
     * @var array
     */
    protected $_columnsValueMap;

    protected function setUp()
    {
        $this->_prepareParserMock();
        $this->_prepareFactoryMock();

        $arguments = array(
            'factory' => $this->_factoryMock,
            'parser' =>  $this->_parserMock
        );
        $this->_model = $this->getMockForAbstractClass(
            '\Magento\Backend\Model\Widget\Grid\TotalsAbstract', $arguments, '', true, false, true, array()
        );
        $this->_model->expects($this->any())
            ->method('_countSum')
            ->will($this->returnValue(2));
        $this->_model->expects($this->any())
            ->method('_countAverage')
            ->will($this->returnValue(2));

        $this->_setUpColumns();
    }

    protected function tearDown()
    {
        unset($this->_parserMock);
        unset($this->_factoryMock);
    }

    /**
     * Retrieve test collection
     *
     * @return \Magento\Data\Collection
     */
    protected function _getTestCollection()
    {
        $collection = new \Magento\Data\Collection();
        $items = array(
            new \Magento\Object(array('test1' => '1', 'test2' => '2')),
        );
        foreach ($items as $item) {
            $collection->addItem($item);
        }

        return $collection;
    }

    /**
     * Prepare tested model by setting columns
     */
    protected function _setUpColumns()
    {
        $columns = array(
            'test1' => 'sum',
            'test2' => 'avg',
            'test3' => 'test1+test2',
            'test4' => 'test1-test2',
            'test5' => 'test1*test2',
            'test6' => 'test1/test2',
            'test7' => 'test1/0'
        );

        foreach ($columns as $index => $expression) {
            $this->_model->setColumn($index, $expression);
        }
    }

    /**
     * Prepare parser mock by setting test expressions for columns and operation used
     */
    protected function _prepareParserMock()
    {
        $this->_parserMock = $this->getMock(
            '\Magento\Backend\Model\Widget\Grid\Parser',
            array('parseExpression', 'isOperation'), array(), '', false, false, false
        );

        $columnsValueMap = array(
            array('test1+test2', array('test1', 'test2', '+')),
            array('test1-test2', array('test1', 'test2', '-')),
            array('test1*test2', array('test1', 'test2', '*')),
            array('test1/test2', array('test1', 'test2', '/')),
            array('test1/0',     array('test1', '0', '/'))
        );
        $this->_parserMock->expects($this->any())
            ->method('parseExpression')
            ->will($this->returnValueMap($columnsValueMap));

        $isOperationValueMap = array(
            array('+', true),
            array('-', true),
            array('*', true),
            array('/', true),
            array('test1', false),
            array('test2', false),
            array('0', false)
        );
        $this->_parserMock->expects($this->any())
            ->method('isOperation')
            ->will($this->returnValueMap($isOperationValueMap));
    }

    /**
     * Prepare factory mock for setting possible values
     */
    protected function _prepareFactoryMock()
    {
        $this->_factoryMock = $this->getMock(
            'Magento\Object\Factory', array('create'), array(), '', false, false, false
        );

        $createValueMap = array(
            array(
                array('test1' => 2, 'test2' => 2, 'test3' => 4, 'test4' => 0, 'test5' => 4, 'test6' => 1, 'test7' => 0),
                new \Magento\Object(
                    array(
                        'test1' => 2, 'test2' => 2, 'test3' => 4, 'test4' => 0, 'test5' => 4, 'test6' => 1, 'test7' => 0
                    )
                ),
            ),
            array(
                array(),
                new \Magento\Object()
            )
        );
        $this->_factoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap($createValueMap));
    }

    public function testColumns()
    {
        $expected = array(
            'test1' => 'sum',
            'test2' => 'avg',
            'test3' => 'test1+test2',
            'test4' => 'test1-test2',
            'test5' => 'test1*test2',
            'test6' => 'test1/test2',
            'test7' => 'test1/0'
        );

        $this->assertEquals($expected, $this->_model->getColumns());
    }

    public function testCountTotals()
    {
        $expected = new \Magento\Object(
            array('test1' => 2, 'test2' => 2, 'test3' => 4, 'test4' => 0, 'test5' => 4, 'test6' => 1, 'test7' => 0)
        );
        $this->assertEquals($expected, $this->_model->countTotals($this->_getTestCollection()));
    }

    public function testReset()
    {
        $this->_model->countTotals($this->_getTestCollection());
        $this->_model->reset();

        $this->assertEquals(new \Magento\Object(), $this->_model->getTotals());
        $this->assertNotEmpty($this->_model->getColumns());
    }

    public function testResetFull()
    {
        $this->_model->countTotals($this->_getTestCollection());
        $this->_model->reset(true);

        $this->assertEquals(new \Magento\Object(), $this->_model->getTotals());
        $this->assertEmpty($this->_model->getColumns());
    }
}
