<?php
/**
 * Aggregate of multiple service exceptions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

class AggregateException extends Exception
{
    protected $_listOfExceptions = [];

    /**
     * Returns the list of exceptions.
     * @return array of exceptions that are stored
     */
    public function getExceptions()
    {
        return $this->_listOfExceptions;
    }

    /**
     * Add an exception to the aggregate list.
     * @param Exception $exception
     */
    public function pushException(Exception $exception)
    {
        $this->_listOfExceptions[] = $exception;
    }

    /**
     * Returns true if the aggregate list contains exceptions.
     * @return bool
     */
    public function hasExceptions()
    {
        return !empty($this->_listOfExceptions);
    }
}