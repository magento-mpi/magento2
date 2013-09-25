<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Email_Template_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Email_Template_Config_Reader
     */
    protected $_model;

    /**
     * @var Magento_Catalog_Model_Attribute_Config_Converter|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var Magento_Core_Model_Module_Dir_ReverseResolver|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleNameResolver;

    protected function setUp()
    {
        $fileResolver = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolver
            ->expects($this->once())
            ->method('get')
            ->with('email_templates.xml', 'scope')
            ->will($this->returnValue(array(
                __DIR__ . '/_files/Fixture/ModuleOne/etc/email_templates_one.xml',
                __DIR__ . '/_files/Fixture/ModuleTwo/etc/email_templates_two.xml',
            )))
        ;

        $this->_converter = $this->getMock('Magento_Core_Model_Email_Template_Config_Converter', array('convert'));

        $moduleReader = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getModuleDir'), array(), '', false
        );
        $moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Core')
            ->will($this->returnValue('stub'))
        ;
        $schemaLocator = new Magento_Core_Model_Email_Template_Config_SchemaLocator($moduleReader);

        $validationState = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationState->expects($this->once())->method('isValidated')->will($this->returnValue(false));

        $this->_moduleNameResolver = $this->getMock(
            'Magento_Core_Model_Module_Dir_ReverseResolver', array(), array(), '', false
        );

        $this->_model = new Magento_Core_Model_Email_Template_Config_Reader(
            $fileResolver,
            $this->_converter,
            $schemaLocator,
            $validationState,
            $this->_moduleNameResolver
        );
    }

    public function testRead()
    {
        $this->_moduleNameResolver
            ->expects($this->at(0))
            ->method('getModuleName')
            ->with(__DIR__ . '/_files/Fixture/ModuleOne/etc/email_templates_one.xml')
            ->will($this->returnValue('Fixture_ModuleOne'))
        ;
        $this->_moduleNameResolver
            ->expects($this->at(1))
            ->method('getModuleName')
            ->with(__DIR__ . '/_files/Fixture/ModuleTwo/etc/email_templates_two.xml')
            ->will($this->returnValue('Fixture_ModuleTwo'))
        ;
        $constraint = function (DOMDOcument $actual) {
            try {
                $expected = __DIR__ . '/_files/email_templates_merged.xml';
                PHPUnit_Framework_Assert::assertXmlStringEqualsXmlFile($expected, $actual->saveXML());
                return true;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };
        $expectedResult = new stdClass();
        $this->_converter
            ->expects($this->once())
            ->method('convert')
            ->with($this->callback($constraint))
            ->will($this->returnValue($expectedResult))
        ;
        $this->assertSame($expectedResult, $this->_model->read('scope'));
    }

}
