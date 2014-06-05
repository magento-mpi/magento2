<?php
/**
 * Base service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Exception;

abstract class Exception extends \Magento\Webapi\ServiceException
{
    /**
     * @var array
     */
    protected $_params = array();

    /**
     * Returns the parameters detailing specifics of this Exception
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
}
