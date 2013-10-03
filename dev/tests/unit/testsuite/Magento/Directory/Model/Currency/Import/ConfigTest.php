<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Model\Currency\Import;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Directory\Model\Currency\Import\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Directory\Model\Currency\Import\Config(array(
            'service_one' => array('class' => 'Service_One', 'label' => 'Service One'),
            'service_two' => array('class' => 'Service_Two', 'label' => 'Service Two'),
        ));
    }

    /**
     * @param array $configData
     * @param string $expectedException
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException(array $configData, $expectedException)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedException);
        new \Magento\Directory\Model\Currency\Import\Config($configData);
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'numeric name' => array(
                array(0 => array('label' => 'Test Label', 'class' => 'Test_Class')),
                'Name for a currency import service has to be specified'
            ),
            'empty name' => array(
                array('' => array('label' => 'Test Label', 'class' => 'Test_Class')),
                'Name for a currency import service has to be specified'
            ),
            'missing class' => array(
                array('test' => array('label' => 'Test Label')),
                'Class for a currency import service has to be specified'
            ),
            'empty class' => array(
                array('test' => array('label' => 'Test Label', 'class' => '')),
                'Class for a currency import service has to be specified'
            ),
            'missing label' => array(
                array('test' => array('class' => 'Test_Class')),
                'Label for a currency import service has to be specified'
            ),
            'empty label' => array(
                array('test' => array('class' => 'Test_Class', 'label' => '')),
                'Label for a currency import service has to be specified'
            ),
        );
    }

    public function testGetAvailableServices()
    {
        $this->assertEquals(array('service_one', 'service_two'), $this->_model->getAvailableServices());
    }

    /**
     * @param string $serviceName
     * @param mixed $expectedResult
     * @dataProvider getServiceClassDataProvider
     */
    public function testGetServiceClass($serviceName, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->getServiceClass($serviceName));
    }

    public function getServiceClassDataProvider()
    {
        return array(
            'known'     => array('service_one', 'Service_One'),
            'unknown'   => array('unknown', null),
        );
    }

    /**
     * @param string $serviceName
     * @param mixed $expectedResult
     * @dataProvider getServiceLabelDataProvider
     */
    public function testGetServiceLabel($serviceName, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->getServiceLabel($serviceName));
    }

    public function getServiceLabelDataProvider()
    {
        return array(
            'known'     => array('service_one', 'Service One'),
            'unknown'   => array('unknown', null),
        );
    }
}
