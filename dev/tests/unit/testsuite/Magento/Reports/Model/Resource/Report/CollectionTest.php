<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model\Resource\Report;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reports\Model\Resource\Report\Collection
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_factoryMock = $this->getMock('\Magento\Reports\Model\DateFactory', array('create'), array(), '', false);
        $arguments = array(
            'dateFactory' => $this->_factoryMock,
        );
        $this->_model = $helper->getObject('Magento\Reports\Model\Resource\Report\Collection', $arguments);
    }

    public function testGetIntervalsWithoutSpecifiedPeriod()
    {
        $startDate = date('m/d/Y', strtotime('-3 day'));
        $endDate = date('m/d/Y', strtotime('+3 day'));
        $this->_model->setInterval($startDate, $endDate);

        $startDateMock = $this->getMock('Magento\Stdlib\DateTime\DateInterface', array(), array(), '', false);
        $endDateMock = $this->getMock('Magento\Stdlib\DateTime\DateInterface', array(), array(), '', false);
        $map = array(
            array($startDate, null, null, $startDateMock),
            array($endDate, null, null, $endDateMock),
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
