<?php
/**
 * Constraint that checks if an object is a model.
 */
class Mage_Test_Constraint_IsMageModel extends PHPUnit_Framework_Constraint
{
    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
        return $other instanceof Mage_Core_Model_Abstract;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'is a model';
    }
}

/**
 * Constraint that checks if an object is a resource model.
 */
class Mage_Test_Constraint_IsMageResourceModel extends PHPUnit_Framework_Constraint
{
    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
        return $other instanceof Mage_Core_Model_Resource_Abstract;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'is a resource model';
    }
}

/**
 * Constraint that checks if an object is a resource collection.
 */
class Mage_Test_Constraint_IsMageResourceCollection extends PHPUnit_Framework_Constraint
{
    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
        // @TODO Actually it should check if it implements necessary interfaces
        return $other instanceof Varien_Data_Collection;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'is a resource collection';
    }
}
