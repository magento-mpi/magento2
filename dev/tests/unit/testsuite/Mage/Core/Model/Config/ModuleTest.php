<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Module.
 */
class Mage_Core_Model_Config_ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $inputConfigFile
     * @param string $expectedConfigFile
     * @param array $allowedModules
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($inputConfigFile, $expectedConfigFile, $allowedModules = array())
    {
        $model = new Mage_Core_Model_Config_Module(
            new Mage_Core_Model_Config_Base($inputConfigFile),
            $allowedModules
        );
        $this->assertXmlStringEqualsXmlFile($expectedConfigFile, $model->getXmlString());
    }

    public function constructorDataProvider()
    {
        return array(
            'sorting dependencies' => array(
                __DIR__ . '/_files/module_input.xml',
                __DIR__ . '/_files/module_sorted.xml',
            ),
            'disallowed modules' => array(
                __DIR__ . '/_files/module_input.xml',
                __DIR__ . '/_files/module_filtered.xml',
                array('Fixture_ModuleOne', 'Fixture_ModuleTwo'),
            ),
        );
    }

    /**
     * @param string $inputConfigFile
     * @param string $expectedException
     * @param string $expectedExceptionMsg
     * @param array $allowedModules
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException(
        $inputConfigFile, $expectedException, $expectedExceptionMsg, $allowedModules = array()
    ) {
        $this->setExpectedException($expectedException, $expectedExceptionMsg);
        new Mage_Core_Model_Config_Module(
            new Mage_Core_Model_Config_Base($inputConfigFile),
            $allowedModules
        );
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'linear dependency' => array(
                __DIR__ . '/_files/module_dependency_linear_input.xml',
                'Mage_Core_exception',
                'Module "Fixture_Module" requires module "Fixture_NonExistingModule".',
            ),
            'circular dependency' => array(
                __DIR__ . '/_files/module_dependency_circular_input.xml',
                'Mage_Core_exception',
                'Module "Fixture_ModuleTwo" cannot depend on "Fixture_ModuleOne".',
            ),
            'soft circular dependency' => array(
                __DIR__ . '/_files/module_dependency_circular_soft_input.xml',
                'Mage_Core_exception',
                'Module "Fixture_ModuleTwo" cannot depend on "Fixture_ModuleOne".',
            ),
            'wrong dependency type' => array(
                __DIR__ . '/_files/module_dependency_wrong_input.xml',
                'UnexpectedValueException',
                'Unsupported value of the XML attribute "type".',
            ),
            'dependency on disallowed module' => array(
                __DIR__ . '/_files/module_input.xml',
                'Mage_Core_exception',
                'Module "Fixture_ModuleTwo" requires module "Fixture_ModuleOne".',
                array('Fixture_ModuleTwo', 'Fixture_ModuleThree')
            )
        );
    }
}
