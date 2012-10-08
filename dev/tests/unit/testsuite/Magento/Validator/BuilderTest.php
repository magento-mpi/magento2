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
        foreach ($expectedCallback as $return) {
            self::$_callbackObject->expects($this->once())
                ->method('getValue')
                ->will($this->returnValue($return));
        }
        $builder = new Magento_Validator_Builder($constraints);
        $builder->createValidator();
        $this->assertEquals($constructorData, Magento_Validator_Stub::$constructorData);
    }

    /**
     * @return array
     */
    public function getBuilderData()
    {
        self::$_callbackObject = $this->getMock('Magento_Validator_Constraint_Option_Callback',
            array('getValue'), array(), '', false);
        return array(
            'constructor_argument' => array(
                'constraints' => array(
                    0 => array (
                        'alias' => 'notEmpty',
                        'class' => 'Magento_Validator_Stub',
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
                        'class' => 'Magento_Validator_Stub',
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
                        'class' => 'Magento_Validator_Stub',
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
            )
        );
    }
}

/**
 * Stub for testing Magento_Validator_Builder
 */
class Magento_Validator_Stub implements Magento_Validator_Interface
{
    /**
     * @var array
     */
    public static $constructorData;

    /**
     * @var array
     */
    protected $_data;

    /**
     * Class constructor
     */
    public function __construct()
    {
        self::$constructorData = func_get_args();
    }

    /**
     * Implementation isValid from interface
     *
     * @param $value
     * @return bool
     */
    public function isValid($value)
    {
        return (bool)$value;
    }

    /**
     * Implementation getMessages from interface
     *
     * @return array
     */
    public function getMessages()
    {
        return array();
    }

    /**
     * Set validator's options
     *
     * @return array
     */
    public function setData()
    {
        return $this->_data = func_get_args();
    }
}
