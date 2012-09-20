<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract API request decorator
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Webapi_Model_Request_DecoratorAbstract
{
    /** @var Mage_Webapi_Model_Request */
    protected $_decoratedRequest;

    /**
     * Decorate given request object with custom API type functionality.
     *
     * @param Mage_Webapi_Model_Request $decoratedRequest
     */
    public function __construct(Mage_Webapi_Model_Request $decoratedRequest)
    {
        $this->_decoratedRequest = $decoratedRequest;
    }

    /**
     * Proxy method calls to decorated object
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->_decoratedRequest, $method), $arguments);
    }

    /**
     * Identify versions of modules that should be used for API configuration file generation.
     * This method should be implemented in concrete API type request decorator.
     *
     * @return array
     */
    abstract function getRequestedModules();
}
