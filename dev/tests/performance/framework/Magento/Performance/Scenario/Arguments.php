<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class encapsulates read-only performance scenario arguments
 */
class Magento_Performance_Scenario_Arguments extends ArrayObject
{
    /**#@+
     * Common scenario arguments
     */
    const ARG_USERS           = 'users';
    const ARG_LOOPS           = 'loops';
    const ARG_HOST            = 'host';
    const ARG_PATH            = 'path';
    const ARG_BASEDIR         = 'basedir';
    const ARG_ADMIN_USERNAME  = 'admin_username';
    const ARG_ADMIN_PASSWORD  = 'admin_password';
    const ARG_ADMIN_FRONTNAME = 'admin_frontname';
    /**#@-*/

    /**
     * Constructor
     *
     * @param array $arguments
     * @throws UnexpectedValueException
     */
    public function __construct(array $arguments)
    {
        $arguments += array(self::ARG_USERS => 1, self::ARG_LOOPS => 1);
        foreach (array(self::ARG_USERS, self::ARG_LOOPS) as $argName) {
            if (!is_int($arguments[$argName]) || $arguments[$argName] < 1) {
                throw new UnexpectedValueException("Scenario argument '$argName' must be a positive integer.");
            }
        }
        parent::__construct($arguments);
    }

    /**
     * Retrieve number of concurrent threads
     *
     * @return integer
     */
    public function getUsers()
    {
        return $this[self::ARG_USERS];
    }

    /**
     * Retrieve number of scenario execution loops
     *
     * @return integer
     */
    public function getLoops()
    {
        return $this[self::ARG_LOOPS];
    }

    /**
     * Deny assignment operator
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($offset, $value)
    {
        $this->_denyModification($offset);
    }

    /**
     * Deny invocation of unset() function
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->_denyModification($offset);
    }

    /**
     * Deny modification operation by throwing an exception
     *
     * @param mixed $index
     * @throws LogicException
     */
    protected function _denyModification($index)
    {
        throw new LogicException("Scenario argument '$index' is read-only.");
    }
}
