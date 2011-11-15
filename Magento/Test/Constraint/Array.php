<?php

/**
 *
 */
class Magento_Test_Constraint_Array extends PHPUnit_Framework_Constraint
{
    protected $arrayKey;

    protected $constraint;

    protected $value;

    /**
     * @param PHPUnit_Framework_Constraint $constraint
     * @param string                       $arrayKey
     */
    public function __construct($arrayKey, PHPUnit_Framework_Constraint $constraint)
    {
        $this->constraint  = $constraint;
        $this->arrayKey    = $arrayKey;
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
        if (!array_key_exists($this->arrayKey, $other)) {
            return false;
        }

        $this->value = $other[$this->arrayKey];

        return $this->constraint->evaluate($other[$this->arrayKey]);
    }

    /**
     * @param   mixed   $other The value passed to evaluate() which failed the
     *                         constraint check.
     * @param   string  $description A string with extra description of what was
     *                               going on while the evaluation failed.
     * @param   boolean $not Flag to indicate negation.
     * @throws  PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, $not = FALSE)
    {
        parent::fail($other[$this->arrayKey], $description, $not);
    }


    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'the value of key "' . $this->arrayKey . '"(' . $this->value . ') ' .  $this->constraint->toString();
    }


    /**
     * Counts the number of constraint elements.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->constraint) + 1;
    }


    protected function customFailureDescription ($other, $description, $not)
    {
        return sprintf('Failed asserting that %s.', $this->toString());
    }
}
