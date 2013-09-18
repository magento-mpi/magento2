<?php
/**
 * Generic exception for usage in services implementation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service;

class Exception extends \Magento\Core\Exception
{
    /** @var array */
    protected $_parameters;

    /**
     * {@inheritdoc}
     * @param array $parameters - Array of optional exception parameters.
     */
    public function __construct($message = "", $code = 0, Exception $previous = null, $parameters = array())
    {
        parent::__construct($message, $code, $previous);
        $this->_parameters = $parameters;
    }

    /**
     * Return the optional list of parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }
}
