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
     * Create and return new model instance
     *
     * @param string $inputConfigFile
     * @param array $allowedModules
     * @return Mage_Core_Model_Config_Module
     */
    protected function _createModel($inputConfigFile, array $allowedModules = array())
    {
        $translateCallback = function () {
            $translator = new Mage_Core_Model_Translate();
            return $translator->translate(func_get_args());
        };
        $helper = $this->getMock('Mage_Core_Helper_Data', array('__'));
        $helper
            ->expects($this->any())
            ->method('__')
            ->will($this->returnCallback($translateCallback))
        ;
        return new Mage_Core_Model_Config_Module(
            new Mage_Core_Model_Config_Base($inputConfigFile), $allowedModules, $helper
        );
    }

    /**
     * @param string $inputConfigFile
     * @param string $expectedConfigFile
     * @param array $allowedModules
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($inputConfigFile, $expectedConfigFile, $allowedModules = array())
    {
        $model = $this->_createModel($inputConfigFile, $allowedModules);
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
        $this->_createModel($inputConfigFile, $allowedModules);
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
