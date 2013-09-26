<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_Utility_XsdValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Utility_XsdValidator
     */
    protected $_validator;

    /**
     * Path to xsd schema file
     * @var string
     */
    protected $_xsdSchema;

    protected function setUp()
    {
        $this->_validator = new Magento_TestFramework_Utility_XsdValidator();
        $this->_xsdSchema = realpath( __DIR__ . '/_files/valid.xsd');
    }

    public function testValidXml()
    {
        $xmlFile = realpath( __DIR__ . '/_files/valid.xml');;
        $xmlString = file_get_contents($xmlFile);

        $this->assertEquals(array(), $this->_validator->validate($this->_xsdSchema, $xmlString));
    }

    public function testInvalidXml()
    {
        $xmlFile = realpath( __DIR__ . '/_files/invalid.xml');
        $expected = array(
            "Element 'block', attribute 'type': The attribute 'type' is not allowed.",
            "Element 'actions': This element is not expected. Expected is ( property ).",
        );
        $xmlString = file_get_contents($xmlFile);

        $this->assertEquals($expected, $this->_validator->validate($this->_xsdSchema, $xmlString));
    }
}
