<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper
     */
    protected $_model;

    /**
     * Path to fixtures
     *
     * @var string
     */
    protected $_fixturePath;

    public function setUp()
    {
        $this->_model = new Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper();
        $this->_fixturePath = realpath(__DIR__ . '/../../../../')
            . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'loader'
            . DIRECTORY_SEPARATOR . 'resource'
            . DIRECTORY_SEPARATOR . 'configReader'
            . DIRECTORY_SEPARATOR . 'xml'
            . DIRECTORY_SEPARATOR;
    }

    public function testMap()
    {
        $xmlAsArray = require ($this->_fixturePath . 'xmlAsArray.php');
        $actual = require ($this->_fixturePath . 'result.php');
        $expected = $this->_model->map($xmlAsArray);
        $this->assertEquals($actual, $expected);
    }
}
