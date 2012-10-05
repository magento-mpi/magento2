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
    protected static $_model = null;

    public static function setUpBeforeClass()
    {
        self::$_model = new Magento_Validator_Config(glob(__DIR__ . '/_files/validation/positive/*/validation.xml'));
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
        self::$_model->createValidator('invalid_entity', null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown validation group "invalid_group" in entity "test_entity"
     */
    public function testCreateValidatorInvalidGroupName()
    {
        self::$_model->createValidator('test_entity', 'invalid_group');
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
     * Check xsd schema rules
     *
     * @dataProvider getValidationRulesForInvalidXmlDataProvider
     * @expectedException Magento_Exception
     * @param string $configFile
     */
    public function testCreateConfigForInvalidXml($configFile)
    {
        $this->markTestSkipped('This test should be included when xsd file will be ready');
        $configFile = array($configFile);
        new Magento_Validator_Config($configFile);
    }

    public function getValidationRulesForInvalidXmlDataProvider()
    {
        return array(
            array(__DIR__ . '/_files/validation/negative/no_constraint.xml'),
            array(__DIR__ . '/_files/validation/negative/not_unique_use.xml'),
            array(__DIR__ . '/_files/validation/negative/no_rule_for_reference.xml'),
            array(__DIR__ . '/_files/validation/negative/no_name_for_entity.xml'),
            array(__DIR__ . '/_files/validation/negative/no_name_for_rule.xml'),
            array(__DIR__ . '/_files/validation/negative/no_name_for_group.xml'),
            array(__DIR__ . '/_files/validation/negative/no_class_for_constraint.xml')
        );
    }

    /**
     * @dataProvider getValidationRulesDataProvider
     * @param string $entityName
     * @param string $groupName
     * @param array $expectedRules
     */
    public function testGetValidationRules($entityName, $groupName, $expectedRules)
    {
        $this->markTestSkipped('This test should be updated');
        $actualRules = self::$_model->getValidationRules($entityName, $groupName);
        $this->assertRulesEqual($expectedRules, $actualRules);
    }

    /**
     * Assert that all expected validation rules are present with correct constraint objects.
     *
     * @param array $expectedRules
     * @param array $actualRules
     */
    public static function assertRulesEqual(array $expectedRules, array $actualRules)
    {
        foreach ($expectedRules as $expectedRule => $expectedConstraints) {
            self::assertArrayHasKey($expectedRule, $actualRules);

            foreach ($expectedConstraints as $expectedConstraint) {
                $constraintFound = false;
                foreach ($actualRules[$expectedRule] as $actualConstraint) {
                    if ($expectedConstraint['constraint'] instanceof $actualConstraint['constraint']) {
                        $constraintFound = true;
                        if (isset($expectedConstraint['field'])) {
                            self::assertArrayHasKey('field', $actualConstraint);
                            self::assertEquals($expectedConstraint['field'], $actualConstraint['field']);
                        }
                        break;
                    }
                }
                if (!$constraintFound) {
                    self::fail(sprintf('Expected constraint "%s" was not found in the rule "%"',
                        get_class($expectedConstraint['constraint']), $expectedRule));
                }
            }
        }
    }

    public function getValidationRulesDataProvider()
    {
        $groupARules = array(
            'test_rule_zend' => array(
                array(
                    'constraint' => $this->getMock('Zend_Validate_Alnum'),
                    'field' => 'test_field'
                ),
            ),
            'test_rule_constraint' => array(
                array(
                    'constraint' => $this->getMock('Magento_Validator_Test'),
                ),
            ),
        );
        $groupBRules = array(
            'test_rule_constraint' => array(
                array(
                    'constraint' => $this->getMock('Magento_Validator_Test'),
                ),
            ),
            'test_rule_constraint_2' => array(
                array(
                    'constraint' => $this->getMock('Magento_Validator_Test'),
                    'field' => 'constraint_field'
                ),
            ),
        );
        $groupCRules = array(
            'test_rule' => array(
                array(
                    'constraint' => $this->getMock('Zend_Validate_Int'),
                    'field' => 'test_field'
                ),
            ),
        );

        return array(
            array('test_entity', 'test_group_a', $groupARules),
            array('test_entity', 'test_group_b', $groupBRules),
            array('test_entity_b', 'test_group_c', $groupCRules),
        );
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
    }
}