<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Converter
     */
    protected $_model;

    public function setUp()
    {
        $factoryMock = $this->getMock('Mage_Backend_Model_Config_Structure_Mapper_Factory',
            array(),
            array(),
            '',
            false,
            false
        );

        $mapperMock = $this->getMock('Mage_Backend_Model_Config_Structure_Mapper_Dependencies',
            array(),
            array(),
            '',
            false,
            false
        );
        $mapperMock->expects($this->any())->method('map')->will($this->returnArgument(0));
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($mapperMock));

        $this->_model = new Mage_Backend_Model_Config_Structure_Converter($factoryMock);
    }

    public function testConvertCorrectlyConvertsConfigStructureToArray()
    {
        $testDom = dirname(dirname(__DIR__)) . '/_files/system_2.xml';
        $dom = new DOMDocument();
        $dom->load($testDom);
        $expectedArray = include dirname(dirname(__DIR__)) . '/_files/converted_config.php';
        $this->assertEquals($expectedArray, $this->_model->convert($dom));
    }
}
