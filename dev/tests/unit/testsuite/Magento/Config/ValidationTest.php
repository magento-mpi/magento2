<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Config_ValidationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Config_Validation
     */
    protected static $_model = null;

    public static function setUpBeforeClass()
    {
        self::$_model = new Magento_Config_Validation(glob(__DIR__ . '/_files/validation/*/validation.xml'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructException()
    {
        new Magento_Config_Validation(array());
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testGetValidationRulesInvalidEntityName()
    {
        self::$_model->getValidationRules('invalid_entity', null);
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testGetValidationRulesInvalidGroupName()
    {
        self::$_model->getValidationRules('test_entity', 'invalid_group');
    }

    public function testGetValidationRules()
    {
        $expectedRules = array(
            'test_rule_zend' => array(
                'constraints' => array(
                    array(
                        'class' => 'Zend_Validate_Alnum',
                        'field' => 'test_field'
                    ),
                )
            ),
            'test_rule_constraint' => array(
                'constraints' => array(
                    array(
                        'class' => 'Magento_Validator_Constraint',
                        'field' => ''
                    ),
                    array(
                        'class' => 'Magento_Validator_Constraint_Test',
                        'field' => ''
                    ),
                    array(
                        'class' => 'Magento_Validator_Constraint_2',
                        'field' => ''
                    )
                )
            ),
        );

        $actualRules = self::$_model->getValidationRules('test_entity', 'test_group_a');
        $this->assertEquals($expectedRules, $actualRules);
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
    }
}