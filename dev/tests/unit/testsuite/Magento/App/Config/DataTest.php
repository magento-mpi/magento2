<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\Data
     */
    protected $_model;

    /**
     * @var \Magento\App\Config\MetadataProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_metaDataProcessor;

    protected function setUp()
    {
        $this->_metaDataProcessor = $this->getMock(
            'Magento\App\Config\MetadataProcessor',
            array(),
            array(),
            '',
            false
        );
        $this->_metaDataProcessor->expects($this->any())
            ->method('process')
            ->will($this->returnArgument(0));
        $this->_model = new \Magento\App\Config\Data($this->_metaDataProcessor, array());
    }

    /**
     * @param string $path
     * @param mixed $value
     * @dataProvider setValueDataProvider
     */
    public function testSetValue($path, $value)
    {
        $this->_model->setValue($path, $value);
        $this->assertEquals($value, $this->_model->getValue($path));
    }

    public function setValueDataProvider()
    {
        return array(
            'simple value' => array(
                'some/config/value',
                'test'
            ),
            'complex value' => array(
                'some/config/value',
                array('level1' => array('level2' => 'test'))
            ),
        );
    }

    public function testGetData()
    {
        $model = new \Magento\App\Config\Data(
            $this->_metaDataProcessor,
            array(
                'test' => array(
                    'path' => 'value',
                )
            )
        );
        $this->assertEquals('value', $model->getValue('test/path'));
    }
}
