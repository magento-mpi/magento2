<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reports_Model_Resource_Report_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Reports_Model_Resource_Report_Collection
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_factoryMock = $this->getMock('Zend_DateFactory', array('create'), array(), '', false);
        $arguments = array(
            'dateFactory' => $this->_factoryMock,
        );
        $this->_model = $helper->getObject('Magento_Reports_Model_Resource_Report_Collection', $arguments);
    }

    public function testGetIntervalsWithoutSpecifiedPeriod()
    {
        $startDate = date('m/d/Y', strtotime('-3 day'));
        $endDate = date('m/d/Y', strtotime('+3 day'));
        $this->_model->setInterval($startDate, $endDate);

        $startDateMock = $this->getMock('Zend_Date', array(), array(), '', false);
        $endDateMock = $this->getMock('Zend_Date', array(), array(), '', false);
        $map = array(
            array(array('date' => $startDate), $startDateMock),
            array(array('date' => $endDate), $endDateMock),
        );
        $this->_factoryMock->expects($this->exactly(2))->method('create')->will($this->returnValueMap($map));

        $startDateMock->expects($this->once())->method('compare')->with($endDateMock)->will($this->returnValue(true));

        $this->assertEquals(0, $this->_model->getSize());
    }

    public function testGetIntervalsWithoutSpecifiedInterval()
    {
        $this->_factoryMock->expects($this->never())->method('create');
        $this->assertEquals(0, $this->_model->getSize());
    }
}