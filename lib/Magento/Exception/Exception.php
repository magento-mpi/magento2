<?php
/**
 * Base service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Exception;

abstract class Exception extends \Exception
{
    protected $params;

    public function __construct($code, $message) {
        parent::__construct($code, $message);
        $params = array();
    }

    /**
     * Returns the parameters detailing specifics of this Exception
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
