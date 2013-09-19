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

namespace Magento\Backend\Model\Config\Structure;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Converter
     */
    protected $_model;

    public function setUp()
    {
        $factoryMock = $this->getMock('Magento\Backend\Model\Config\Structure\Mapper\Factory',
            array(),
            array(),
            '',
            false,
            false
        );

        $mapperMock = $this->getMock('Magento\Backend\Model\Config\Structure\Mapper\Dependencies',
            array(),
            array(),
            '',
            false,
            false
        );
        $mapperMock->expects($this->any())->method('map')->will($this->returnArgument(0));
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($mapperMock));

        $this->_model = new \Magento\Backend\Model\Config\Structure\Converter($factoryMock);
    }

    public function testConvertCorrectlyConvertsConfigStructureToArray()
    {
        $testDom = dirname(dirname(__DIR__)) . '/_files/system_2.xml';
        $dom = new \DOMDocument();
        $dom->load($testDom);
        $expectedArray = include dirname(dirname(__DIR__)) . '/_files/converted_config.php';
        $this->assertEquals($expectedArray, $this->_model->convert($dom));
    }
}
