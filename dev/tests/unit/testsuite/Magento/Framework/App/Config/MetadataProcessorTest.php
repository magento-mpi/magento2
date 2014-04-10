<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config;

class MetadataProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Config\MetadataProcessor
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendModelMock;

    protected function setUp()
    {
        $this->_modelPoolMock = $this->getMock(
            'Magento\Framework\App\Config\Data\ProcessorFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_initialConfigMock = $this->getMock('Magento\Framework\App\Config\Initial', array(), array(), '', false);
        $this->_backendModelMock = $this->getMock('Magento\Framework\App\Config\Data\ProcessorInterface');
        $this->_initialConfigMock->expects(
            $this->any()
        )->method(
            'getMetadata'
        )->will(
            $this->returnValue(array('some/config/path' => array('backendModel' => 'Custom_Backend_Model')))
        );
        $this->_model = new \Magento\Framework\App\Config\MetadataProcessor($this->_modelPoolMock, $this->_initialConfigMock);
    }

    public function testProcess()
    {
        $this->_modelPoolMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'Custom_Backend_Model'
        )->will(
            $this->returnValue($this->_backendModelMock)
        );
        $this->_backendModelMock->expects(
            $this->once()
        )->method(
            'processValue'
        )->with(
            'value'
        )->will(
            $this->returnValue('processed_value')
        );
        $data = array('some' => array('config' => array('path' => 'value')), 'active' => 1);
        $expectedResult = $data;
        $expectedResult['some']['config']['path'] = 'processed_value';
        $this->assertEquals($expectedResult, $this->_model->process($data));
    }
}
