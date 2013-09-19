<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Section\Store;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Core\Model\Config\Section\Store\Converter */
    protected $_model;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_processorMock;

    public function setUp()
    {
        $this->_processorMock = $this->getMock('Magento\Core\Model\Config\Section\Processor\Placeholder',
            array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Config\Section\Store\Converter($this->_processorMock);
    }

    public function testConvert()
    {
        $initial = array('path' => array('to' => array('save' => 'saved value', 'overwrite' => 'old value')));
        $source = array('path/to/overwrite' => 'overwritten', 'path/to/added' => 'added value');
        $mergeResult = array('path' => array('to' => array(
                'save' => 'saved value',
                'overwrite' => 'overwritten',
                'added' => 'added value'
        )));
        $processorResult = '123Value';
        $this->_processorMock->expects($this->once())
            ->method('process')
            ->with($mergeResult)
            ->will($this->returnValue($processorResult));

        $this->assertEquals($processorResult, $this->_model->convert($source, $initial));
    }
}
