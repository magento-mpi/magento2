<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Directory_Model_Currency_Import_Source_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Directory_Model_Currency_Import_Source_Service
     */
    protected $_model;

    /**
     * @var Magento_Directory_Model_Currency_Import_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importConfig;

    protected function setUp()
    {
        $this->_importConfig = $this->getMock(
            'Magento_Directory_Model_Currency_Import_Config', array(), array(), '', false
        );
        $this->_model = new Magento_Directory_Model_Currency_Import_Source_Service($this->_importConfig);
    }

    public function testToOptionArray()
    {
        $this->_importConfig
            ->expects($this->once())
            ->method('getAvailableServices')
            ->will($this->returnValue(array('service_one', 'service_two')))
        ;
        $this->_importConfig
            ->expects($this->at(1))
            ->method('getServiceLabel')
            ->with('service_one')
            ->will($this->returnValue('Service One'))
        ;
        $this->_importConfig
            ->expects($this->at(2))
            ->method('getServiceLabel')
            ->with('service_two')
            ->will($this->returnValue('Service Two'))
        ;
        $expectedResult = array(
            array('value' => 'service_one', 'label' => 'Service One'),
            array('value' => 'service_two', 'label' => 'Service Two'),
        );
        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
        // Makes sure the value is calculated only once
        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
    }
}
