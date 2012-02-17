<?php

/**
 * Custom constraint to access and check array keys
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Constraint_Array extends PHPUnit_Framework_Constraint
{
    /**
     * Key of array
     *
     * @var string
     */
    protected $_arrayKey;

    /**
     * Constraint object
     *
     * @var PHPUnit_Framework_Constraint
     */
    protected $_constraint;

    /**
     * Value from array by array key
     *
     * @var mixed
     */
    protected $_value;

    /**
     * Constructor
     *
     * @param PHPUnit_Framework_Constraint $constraint
     * @param string                       $arrayKey
     */
    public function __construct($arrayKey, PHPUnit_Framework_Constraint $constraint)
    {
        $this->_constraint  = $constraint;
        $this->_arrayKey    = $arrayKey;
    }


    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param  mixed $other Value or object to evaluate.
     * @param  string $description Additional information about the test
     * @param  bool $returnResult Whether to return a result or throw an exception
     * @return bool
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        if (!array_key_exists($this->_arrayKey, $other)) {
            return false;
        }

        $this->_value = $other[$this->_arrayKey];

        return $this->_constraint->evaluate($other[$this->_arrayKey], $description, $returnResult);
    }

    /**
     * Creates the appropriate exception for the constraint which can be caught
     * by the unit test system. This can be called if a call to evaluate()
     * fails.
     *
     * @param   mixed   $other The value passed to evaluate() which failed the
     *                         constraint check.
     * @param   string  $description A string with extra description of what was
     *                               going on while the evaluation failed.
     * @param   PHPUnit_Framework_ComparisonFailure $comparisonFailure
     * @throws  PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, PHPUnit_Framework_ComparisonFailure $comparisonFailure = null)
    {
        parent::fail($other[$this->_arrayKey], $description, $comparisonFailure);
    }


    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'The value of key "' . $this->_arrayKey . '"(' . $this->_value . ') ' .  $this->_constraint->toString();
    }


    /**
     * Counts the number of constraint elements.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_constraint) + 1;
    }
}
