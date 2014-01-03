<?php
/**
 * Base service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

abstract class Exception extends \Exception
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
        $this->params = array();
    }

    /**
     * Returns the parameters detailing specifics of this Exception
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
