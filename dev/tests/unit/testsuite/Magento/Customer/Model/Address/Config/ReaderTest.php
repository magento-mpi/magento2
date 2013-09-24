<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Address_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Model_Address_Config_Reader
     */
    protected $_model;

    /**
     * @var Magento_Config_FileResolverInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolverMock;

    /**
     * @var Magento_Customer_Model_Address_Config_Converter|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var Magento_Customer_Model_Address_Config_SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var Magento_Config_ValidationStateInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validationState;

    protected function setUp()
    {
        $this->_fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $this->_fileResolverMock
            ->expects($this->once())
            ->method('get')
            ->with('address_formats.xml', 'scope')
            ->will($this->returnValue(array(
                __DIR__ . '/_files/formats_one.xml',
                __DIR__ . '/_files/formats_two.xml',
            )));

        $this->_converter = $this->getMock('Magento_Customer_Model_Address_Config_Converter', array('convert'));

        $moduleReader = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getModuleDir'), array(), '', false
        );

        $moduleReader->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Customer')
            ->will($this->returnValue('stub'));

        $this->_schemaLocator = new Magento_Customer_Model_Address_Config_SchemaLocator($moduleReader);
        $this->_validationState = $this->getMock('Magento_Config_ValidationStateInterface');
        $this->_validationState->expects($this->once())->method('isValidated')->will($this->returnValue(false));

        $this->_model = new Magento_Customer_Model_Address_Config_Reader(
            $this->_fileResolverMock,
            $this->_converter,
            $this->_schemaLocator,
            $this->_validationState
        );
    }

    public function testRead()
    {
        $expectedResult = new stdClass();
        $constraint = function (DOMDOcument $actual) {
            try {
                $expected = __DIR__ . '/_files/formats_merged.xml';
                PHPUnit_Framework_Assert::assertXmlStringEqualsXmlFile($expected, $actual->saveXML());
                return true;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };

        $this->_converter
            ->expects($this->once())
            ->method('convert')
            ->with($this->callback($constraint))
            ->will($this->returnValue($expectedResult))
        ;

        $this->assertSame($expectedResult, $this->_model->read('scope'));
    }
}
