<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Stdlib;

/**
 * Utility methods for the boolean data type
 */
class BooleanUtils
{
    /**
     * Expressions that mean boolean TRUE
     *
     * @var array
     */
    private $trueValues;

    /**
     * Expressions that mean boolean FALSE
     *
     * @var array
     */
    private $falseValues;

    /**
     * @param array $trueValues
     * @param array $falseValues
     */
    public function __construct(
        array $trueValues = array(true, 1, 'true', '1'),
        array $falseValues = array(false, 0, 'false', '0')
    ) {
        $this->trueValues = $trueValues;
        $this->falseValues = $falseValues;
    }

    /**
     * Retrieve boolean value for an expression
     *
     * @param mixed $value Boolean expression
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function toBoolean($value)
    {
        /**
         * Built-in function filter_var() is not used, because such values as on/off are irrelevant in some contexts
         * @link http://www.php.net/manual/en/filter.filters.validate.php
         */
        if (in_array($value, $this->trueValues, true)) {
            return true;
        }
        if (in_array($value, $this->falseValues, true)) {
            return false;
        }
        $allowedValues = array_merge($this->trueValues, $this->falseValues);
        throw new \InvalidArgumentException(
            'Boolean value is expected, supported values: ' . var_export($allowedValues, true)
        );
    }
}
