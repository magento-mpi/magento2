<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Cart\SalesModel;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Payment\Model\Cart\SalesModel\Factory */
    protected $_model;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMockForAbstractClass('Magento\ObjectManager');
        $this->_model = new \Magento\Payment\Model\Cart\SalesModel\Factory($this->_objectManagerMock);
    }

    /**
     * @param string $salesModelClass
     * @param string $expectedType
     * @dataProvider createDataProvider
     */
    public function testCreate($salesModelClass, $expectedType)
    {
        $salesModel = $this->getMock($salesModelClass, array('__wakeup'), array(), '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $expectedType,
            array('salesModel' => $salesModel)
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals('some value', $this->_model->create($salesModel));
    }

    public function createDataProvider()
    {
        return array(
            array('Magento\Sales\Model\Quote', 'Magento\Payment\Model\Cart\SalesModel\Quote'),
            array('Magento\Sales\Model\Order', 'Magento\Payment\Model\Cart\SalesModel\Order')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalid()
    {
        $this->_model->create('any invalid');
    }
}
