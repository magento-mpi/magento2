<?php

/**
 * Custom constraint to access and check array keys
 */
class Magento_Test_Constraint_Array extends PHPUnit_Framework_Constraint
{
    protected $_arrayKey;

    protected $_constraint;

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
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
        if (!array_key_exists($this->_arrayKey, $other)) {
            return false;
        }

        $this->_value = $other[$this->_arrayKey];

        return $this->_constraint->evaluate($other[$this->_arrayKey]);
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
     * @param   boolean $not Flag to indicate negation.
     * @throws  PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, $not = FALSE)
    {
        parent::fail($other[$this->_arrayKey], $description, $not);
    }


    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'the value of key "' . $this->_arrayKey . '"(' . $this->_value . ') ' .  $this->_constraint->toString();
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

    /**
     * Build custom failure description
     *
     * @param mixed $other
     * @param string $description
     * @param boolean $not
     * @return string
     */
    protected function customFailureDescription($other, $description, $not)
    {
        return sprintf('Failed asserting that %s.', $this->toString());
    }
}
