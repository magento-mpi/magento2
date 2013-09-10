<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for \Magento\Validator\Builder
 */
class Magento_Validator_BuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test createValidator method
     *
     * @dataProvider createValidatorDataProvider
     *
     * @param array $constraints
     * @param \Magento\Validator\ValidatorInterface $expectedValidator
     */
    public function testCreateValidator(array $constraints, $expectedValidator)
    {
        $builder = new \Magento\Validator\Builder($constraints);
        $actualValidator = $builder->createValidator();
        $this->assertEquals($expectedValidator, $actualValidator);
    }

    /**
     * Data provider for
     *
     * @return array
     */
    public function createValidatorDataProvider()
    {
        $result = array();

        /** @var \Magento\Translate\AdapterAbstract $translator */
        $translator = $this->getMockBuilder('Magento\Translate\AdapterAbstract')
            ->getMockForAbstractClass();
        \Magento\Validator\ValidatorAbstract::setDefaultTranslator($translator);

        // Case 1. Check constructor with arguments
        $actualConstraints = array(array(
            'alias' => 'name_alias',
            'class' => 'Magento_Validator_Test_StringLength',
            'options' => array(
                'arguments' => array(1, new \Magento\Validator\Constraint\Option(20))
            ),
            'property' => 'name',
            'type' => 'property',
        ));

        $expectedValidator = new \Magento\Validator();
        $expectedValidator->addValidator(
            new \Magento\Validator\Constraint\Property(
                new Magento_Validator_Test_StringLength(1, 20), 'name', 'name_alias'
            )
        );

        $result[] = array($actualConstraints, $expectedValidator);

        // Case 2. Check method calls
        $actualConstraints = array(array(
            'alias' => 'description_alias',
            'class' => 'Magento_Validator_Test_StringLength',
            'options' => array(
                'methods' => array (
                    array(
                        'method' => 'setMin',
                        'arguments' => array(10)
                    ),
                    array(
                        'method' => 'setMax',
                        'arguments' => array(1000)
                    )
                ),
            ),
            'property' => 'description',
            'type' => 'property',
        ));

        $expectedValidator = new \Magento\Validator();
        $expectedValidator->addValidator(
            new \Magento\Validator\Constraint\Property(
                new Magento_Validator_Test_StringLength(10, 1000), 'description', 'description_alias'
            )
        );

        $result[] = array($actualConstraints, $expectedValidator);

        // Case 3. Check callback on validator
        $actualConstraints = array(array(
            'alias' => 'sku_alias',
            'class' => 'Magento_Validator_Test_StringLength',
            'options' => array(
                'callback' => array(new \Magento\Validator\Constraint\Option\Callback(
                    function ($validator) {
                        $validator->setMin(20);
                        $validator->setMax(100);
                    }
                ))
            ),
            'property' => 'sku',
            'type' => 'property',
        ));

        $expectedValidator = new \Magento\Validator();
        $expectedValidator->addValidator(
            new \Magento\Validator\Constraint\Property(
                new Magento_Validator_Test_StringLength(20, 100), 'sku', 'sku_alias'
            )
        );

        $result[] = array($actualConstraints, $expectedValidator);

        return $result;
    }

    /**
     * Check addConfiguration logic
     *
     * @dataProvider configurationDataProvider
     *
     * @param array $constraints
     * @param string $alias
     * @param array $configuration
     * @param array $expected
     */
    public function testAddConfiguration($constraints, $alias, $configuration, $expected)
    {
        $builder = new \Magento\Validator\Builder($constraints);
        $builder->addConfiguration($alias, $configuration);
        $this->assertAttributeEquals($expected, '_constraints', $builder);
    }

    /**
     * Check addConfigurations logic
     *
     * @dataProvider configurationDataProvider
     *
     * @param array $constraints
     * @param string $alias
     * @param array $configuration
     * @param array $expected
     */
    public function testAddConfigurations($constraints, $alias, $configuration, $expected)
    {
        $builder = new \Magento\Validator\Builder($constraints);
        $configurations = array($alias => array($configuration));
        $builder->addConfigurations($configurations);
        $this->assertAttributeEquals($expected, '_constraints', $builder);
    }

    /**
     * Builder configurations data provider
     *
     * @return array
     */
    public function configurationDataProvider()
    {
        $callback = new \Magento\Validator\Constraint\Option\Callback(
            array('Magento_Validator_Test_Callback', 'getId'));
        $someMethod = array('method' => 'getMessages');
        $methodWithArgs = array('method' => 'setMax', 'arguments' => array(100));
        $constructorArgs = array('arguments' => array(array('max' => '50')));
        $callbackConfig = array('callback' => $callback);

        $configuredConstraint = array(
            'alias' => 'current_alias',
            'class' => 'Magento_Validator_Test_NotEmpty',
            'options' => array(
                'arguments' => array(array('min' => 1)),
                'callback' => array($callback),
                'methods' => array($someMethod)
            ),
            'property' => 'int',
            'type' => 'property'
        );
        $emptyConstraint = array(
            'alias' => 'current_alias',
            'class' => 'Magento_Validator_Test_NotEmpty',
            'options' => null,
            'property' => 'int',
            'type' => 'property'
        );
        $constraintWithArgs = array(
            'alias' => 'current_alias',
            'class' => 'Magento_Validator_Test_NotEmpty',
            'options' => array('arguments' => array(array('min' => 1))),
            'property' => 'int',
            'type' => 'property'
        );
        return array(
            'constraint is unchanged when alias not found' => array(
                array($emptyConstraint), 'some_alias', $someMethod, array($emptyConstraint)),

            'constraint options initialized with method' => array(array($emptyConstraint), 'current_alias', $someMethod,
                array($this->_getExpectedConstraints($emptyConstraint, 'methods', array($someMethod)))),

            'constraint options initialized with callback' => array(array($emptyConstraint), 'current_alias',
                $callbackConfig, array($this->_getExpectedConstraints($emptyConstraint, 'callback', array($callback)))),

            'constraint options initialized with arguments' => array(
                array($emptyConstraint), 'current_alias', $constructorArgs,
                array($this->_getExpectedConstraints($emptyConstraint, 'arguments', array(array('max' => '50'))))
            ),

            'methods initialized' => array(
                array($constraintWithArgs), 'current_alias', $methodWithArgs,
                array($this->_getExpectedConstraints($constraintWithArgs, 'methods', array($methodWithArgs)))
            ),

            'method added' => array(
                array($configuredConstraint), 'current_alias', $methodWithArgs,
                array($this->_getExpectedConstraints($configuredConstraint, 'methods',
                    array($someMethod, $methodWithArgs)))
            ),

            'callback initialized' => array(
                array($constraintWithArgs), 'current_alias', $callbackConfig,
                array($this->_getExpectedConstraints($constraintWithArgs, 'callback', array($callback)))
            ),

            'callback added' => array(
                array($configuredConstraint), 'current_alias', $callbackConfig,
                array($this->_getExpectedConstraints($configuredConstraint, 'callback', array($callback, $callback)))
            ),
        );
    }

    /**
     * Get expected constraint configuration by actual and changes
     *
     * @param array $constraint
     * @param string $optionKey
     * @param mixed $optionValue
     * @return array
     */
    protected function _getExpectedConstraints($constraint, $optionKey, $optionValue)
    {
        if (!is_array($constraint['options'])) {
            $constraint['options'] = array();
        }
        $constraint['options'][$optionKey] = $optionValue;
        return $constraint;
    }

    /**
     * Check arguments validation passed into constructor
     *
     * @dataProvider invalidArgumentsDataProvider
     *
     * @param array $options
     * @param string $exception
     * @param string $exceptionMessage
     */
    public function testConstructorConfigValidation(array $options, $exception, $exceptionMessage)
    {
        $this->setExpectedException($exception, $exceptionMessage);
        if (array_key_exists('method', $options)) {
            $options = array(
                'methods' => array($options)
            );
        }
        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento_Validator_Test_True',
            'options' => $options,
            'type' => 'entity'
        ));
        new \Magento\Validator\Builder($constraints);
    }

    /**
     * Check arguments validation passed into configuration
     *
     * @dataProvider invalidArgumentsDataProvider
     *
     * @param array $options
     * @param string $exception
     * @param string $exceptionMessage
     */
    public function testAddConfigurationConfigValidation(array $options, $exception, $exceptionMessage)
    {
        $this->setExpectedException($exception, $exceptionMessage);

        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento_Validator_Test_True',
            'options' => null,
            'type' => 'entity'
        ));
        $builder = new \Magento\Validator\Builder($constraints);
        $builder->addConfiguration('alias', $options);
    }

    /**
     * Data provider for testing configuration validation
     *
     * @return array
     */
    public function invalidArgumentsDataProvider()
    {
        return array(
            'constructor invalid arguments' => array(
                array(
                    'arguments' => 'invalid_argument'
                ),
                'InvalidArgumentException',
                'Arguments must be an array'
            ),

            'methods invalid arguments' => array(
                array(
                    'method' => 'setValue',
                    'arguments' => 'invalid_argument'
                ),
                'InvalidArgumentException',
                'Method arguments must be an array'
            ),

            'methods invalid format' => array(
                array(
                    'method' => array('name' => 'setValue')
                ),
                'InvalidArgumentException',
                'Method has to be passed as string'
            ),

            'constructor arguments invalid callback' => array(
                array(
                    'callback' => array('invalid', 'callback')
                ),
                'InvalidArgumentException',
                'Callback must be instance of \Magento\Validator\Constraint\Option\Callback'
            )
        );
    }

    /**
     * Check exception is thrown if validator is not an instance of \Magento\Validator\ValidatorInterface
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Constraint class "\Magento\Object" must implement \Magento\Validator\ValidatorInterface
     */
    public function testCreateValidatorInvalidInstance()
    {
        $constraints = array(array(
            'alias' => 'alias',
            'class' => '\Magento\Object',
            'options' => null,
            'type' => 'entity'
        ));
        $builder = new \Magento\Validator\Builder($constraints);
        $builder->createValidator();
    }

    /**
     * Test invalid configuration formats
     *
     * @dataProvider invalidConfigurationFormatDataProvider
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Configuration has incorrect format
     *
     * @param mixed $configuration
     */
    public function testAddConfigurationInvalidFormat($configuration)
    {
        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento_Validator_Test_True',
            'options' => null,
            'type' => 'entity'
        ));
        $builder = new \Magento\Validator\Builder($constraints);
        $builder->addConfigurations($configuration);
    }

    /**
     * Data provider for incorrect configurations
     *
     * @return array
     */
    public function invalidConfigurationFormatDataProvider()
    {
        return array(
            'configuration incorrect method call' => array(
                array(
                    'alias' => array(
                        'method' => array('name' => 'incorrectMethodCall')
                    )
                )
            ),

            'configuration incorrect configuration' => array(
                array(
                    'alias' => array(
                        array(
                            'data' => array('incorrectData')
                        )
                    )
                )
            )
        );
    }
}
