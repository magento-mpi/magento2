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
 * Test for Magento_Validator_Builder
 */
class Magento_Validator_BuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Validator_Constraint_Option_Callback
     */
    protected static $_callbackObject;

    /**
     * Test for Magento_Validator_Builder::createValidator()
     *
     * @param array $constraints
     * @param array $constructorData
     * @param array $expectedCallback
     * @dataProvider getBuilderData
     */
    public function testCreateValidator(array $constraints, array $constructorData, array $expectedCallback = array())
    {
        if (isset($expectedCallback['return'])) {
            self::$_callbackObject->expects($this->at(0))
                ->method('getValue')
                ->will($this->returnValue($expectedCallback['return']));
        } elseif (isset($expectedCallback['entity_callback'])) {
            self::$_callbackObject->expects($this->once())
                ->method('getValue');
            self::$_callbackObject->expects($this->once())
                ->method('setArguments');
        }
        $builder = new Magento_Validator_Builder($constraints);
        $builder->createValidator();
        $this->assertEquals($constructorData, Magento_Validator_Test_Stub::$constructorData);
    }

    /**
     * Data provider for testCreateValidator
     *
     * @return array
     */
    public function getBuilderData()
    {
        self::$_callbackObject = $this->getMock('Magento_Validator_Constraint_Option_Callback',
            array('getValue'), array(), '', false);
        return $this->constructorDataProvider() + $this->methodsDataProvider() + array(
            'callback_entity' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(5, 6),
                            'callback' => array(self::$_callbackObject),
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    ),
                ),
                'constructorData' => array(5, 6),
                'expectedCallback' => array(
                    'entity_callback' => array()
                )
            ),
        );
    }

    /**
     * Return data for testing validator constructor
     *
     * @return array
     */
    public function constructorDataProvider()
    {
        return array(
            'constructor_argument' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(5, 6)
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    )
                ),
                'constructorData' => array(5, 6),
            ),
            'constructor_array' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(5, array(43, 84))
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    )
                ),
                'constructorData' => array(5, array(43, 84)),
            ),
            'constructor_callback' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(
                                self::$_callbackObject,
                                9
                            )
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    )
                ),
                'constructorData' => array(array(7, 8), 9),
                'expectedCallback' => array(
                    'return' => array(7, 8),
                )
            ),
        );
    }

    /**
     * Return data for testing validator methods
     *
     * @return array
     */
    public function methodsDataProvider()
    {
        return array(
            'method_arguments' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(5, 6),
                            'methods' =>
                            array (
                                'setData' =>
                                array (
                                    'method' => 'setData',
                                    'arguments' => array (3, 4),
                                ),
                            ),
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    )
                ),
                'constructorData' => array(3, 4),
            ),
            'method_callback' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(5, 6),
                            'methods' =>
                            array (
                                'setData' =>
                                array (
                                    'method' => 'setData',
                                    'arguments' => array(
                                        self::$_callbackObject,
                                        'Second argument'
                                    ),
                                ),
                            ),
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    )
                ),
                'constructorData' => array(array('First param', 85), 'Second argument'),
                'expectedCallback' => array(
                    'return' => array('First param', 85),
                )
            ),
            'method_array' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Test_Stub',
                        'options' => array(
                            'arguments' => array(5, 6),
                            'methods' =>
                            array (
                                'setData' =>
                                array (
                                    'method' => 'setData',
                                    'arguments' => array (3, array(45, 83)),
                                ),
                            ),
                        ),
                        'property' => 'name',
                        'type' => 'property',
                    )
                ),
                'constructorData' => array (3, array(45, 83)),
            ),
        );
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
                'methods' => array(
                    $options['method'] => $options
                )
            );
        }
        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Magento_Validator_Test_True',
            'options' => $options,
            'type' => 'entity'
        ));
        new Magento_Validator_Builder($constraints);
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
        $builder = new Magento_Validator_Builder($constraints);
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

            'constructor arguments invalid callback' => array(
                array(
                    'callback' => array('invalid', 'callback')
                ),
                'InvalidArgumentException',
                'Callback must be instance of Magento_Validator_Constraint_Option_Callback'
            )
        );
    }

    /**
     * Check exception is thrown if validator is not an instance of Magento_Validator_ValidatorInterface
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Constraint class "Varien_Object" must implement Magento_Validator_ValidatorInterface
     */
    public function testCreateValidatorInvalidInstance()
    {
        $constraints = array(array(
            'alias' => 'alias',
            'class' => 'Varien_Object',
            'options' => null,
            'type' => 'entity'
        ));
        $builder = new Magento_Validator_Builder($constraints);
        $builder->createValidator();
    }
}
