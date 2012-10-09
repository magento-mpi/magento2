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
 * Stub for testing Magento_Validator_Builder
 */
class Magento_Validator_Test_Stub implements Magento_Validator_ValidatorInterface
{
    /**
     * @var array
     */
    public static $constructorData;

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
        self::$constructorData = func_get_args();
    }
}
