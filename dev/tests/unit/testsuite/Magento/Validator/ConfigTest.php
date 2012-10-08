<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Validator_Config
 */
class Magento_Validator_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Validator_Config
     */
    protected $_config = null;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_config = new Magento_Validator_Config(glob(__DIR__ . '/_files/validation/positive/*/validation.xml'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There must be at least one configuration file specified.
     */
    public function testConstructException()
    {
        new Magento_Validator_Config(array());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown validation entity "invalid_entity"
     */
    public function testCreateValidatorInvalidEntityName()
    {
        $this->_config->createValidator('invalid_entity', null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown validation group "invalid_group" in entity "test_entity_a"
     */
    public function testCreateValidatorInvalidGroupName()
    {
        $this->_config->createValidator('test_entity_a', 'invalid_group');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Constraint class "stdClass" must implement Magento_Validator_Interface
     */
    public function testCreateValidatorInvalidConstraintClass()
    {
        $configFile = glob(__DIR__ . '/_files/validation/negative/invalid_constraint.xml');
        $config = new Magento_Validator_Config($configFile);
        $config->createValidator('test_entity', 'test_group');
    }

    /**
     * @dataProvider getValidationRulesDataProvider
     *
     * @param string $entityName
     * @param string $groupName
     * @param mixed $value
     */
    public function testCreateValidator($entityName, $groupName, $value, $expectedResult, $expectedMessages)
    {
        $validator = $this->_config->createValidator($entityName, $groupName);
        $actualResult = $validator->isValid($value);
        $this->assertEquals($expectedMessages, $validator->getMessages());
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for testCreateConfigForInvalidXml
     *
     * @return array
     */
    public function getValidationRulesDataProvider()
    {
        $result = array();

        // Case 1. Pass check alnum and int properties are not empty and have valid value
        $entityName = 'test_entity_a';
        $groupName = 'check_alnum_and_int_not_empty_and_have_valid_value';
        $value = new Varien_Object(array(
            'int' => 1,
            'alnum' => 'abc123'
        ));
        $expectedResult = true;
        $expectedMessages = array();
        $result[] = array($entityName, $groupName, $value, $expectedResult, $expectedMessages);

        // Case 2. Fail check alnum is not empty
        $value = new Varien_Object(array(
            'int' => 'abc123',
            'alnum' => null
        ));
        $expectedResult = false;
        $expectedMessages = array(
            'alnum' => array(
                'isEmpty' => 'Value is required and can\'t be empty',
                'alnumInvalid' => 'Invalid type given. String, integer or float expected',
            ),
            'int' => array(
                'notInt' => '\'abc123\' does not appear to be an integer',
            ),
        );
        $result[] = array($entityName, $groupName, $value, $expectedResult, $expectedMessages);

        // Case 3. Pass check alnum has valid value
        $groupName = 'check_alnum';
        $value = new Varien_Object(array(
            'int' => 'abc123',
            'alnum' => 'abc123'
        ));
        $expectedResult = true;
        $expectedMessages = array();
        $result[] = array($entityName, $groupName, $value, $expectedResult, $expectedMessages);

        // Case 4. Fail check alnum has valid value
        $value = new Varien_Object(array(
            'int' => 'abc123',
            'alnum' => '[abc123]'
        ));
        $expectedResult = false;
        $expectedMessages = array(
            'alnum' => array(
                'notAlnum' => '\'[abc123]\' contains characters which are non alphabetic and no digits'
            )
        );
        $result[] = array($entityName, $groupName, $value, $expectedResult, $expectedMessages);

        // Case 5. Pass check witch custom builder class
        $entityName = 'test_entity_b';
        $groupName = 'custom_builder';
        $value = new Varien_Object();
        $expectedResult = true;
        $expectedMessages = array();
        $result[] = array($entityName, $groupName, $value, $expectedResult, $expectedMessages);

        return $result;
    }



    /**
     * Check XSD schema validates invalid config files
     *
     * @dataProvider getInvalidXmlFiles
     * @expectedException Magento_Exception
     *
     * @param string $configFile
     */
    public function testValidateInvalidConfigFiles($configFile)
    {
        $configFile = array($configFile);
        new Magento_Validator_Config($configFile);
    }

    /**
     * Data provider for testValidateInvalidConfigFiles
     *
     * @return array
     */
    public function getInvalidXmlFiles()
    {
        // TODO: add case There are no "entity_constraints" and "property_constraints" elements inside "rule" element
        return array(
            array(__DIR__ . '/_files/validation/negative/no_constraint.xml'),
            array(__DIR__ . '/_files/validation/negative/not_unique_use.xml'),
            array(__DIR__ . '/_files/validation/negative/no_rule_for_reference.xml'),
            array(__DIR__ . '/_files/validation/negative/no_name_for_entity.xml'),
            array(__DIR__ . '/_files/validation/negative/no_name_for_rule.xml'),
            array(__DIR__ . '/_files/validation/negative/no_name_for_group.xml'),
            array(__DIR__ . '/_files/validation/negative/no_class_for_constraint.xml'),
            array(__DIR__ . '/_files/validation/negative/invalid_method.xml'),
            array(__DIR__ . '/_files/validation/negative/invalid_method_callback.xml'),
            array(__DIR__ . '/_files/validation/negative/invalid_entity_callback.xml'),
            array(__DIR__ . '/_files/validation/negative/invalid_child_for_option.xml'),
            array(__DIR__ . '/_files/validation/negative/invalid_content_for_callback.xml'),
        );
    }

    /**
     * Test schema file exists
     */
    public function testGetSchemaFile()
    {
        $this->assertFileExists($this->_config->getSchemaFile());
    }
}