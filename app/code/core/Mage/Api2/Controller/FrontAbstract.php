<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract front controller for concrete API type.
 */
abstract class Mage_Api2_Controller_FrontAbstract implements Mage_Core_Controller_FrontInterface
{
    /** @var Mage_Api2_Model_Request */
    protected $_request;
    /** @var Mage_Api2_Model_Response */
    protected $_response;
    /** @var Mage_Api2_Model_Config_Resource */
    protected $_resourceConfig;

    abstract public function init();

    abstract public function dispatch();

    /**
     * Retrieve config describing resources available in all APIs
     * The same resource config must be used in all API types
     *
     * @return Mage_Api2_Model_Config_Resource
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Set resource config.
     *
     * @param Mage_Api2_Model_Config_Resource $config
     * @return Mage_Api2_Controller_FrontAbstract
     */
    public function setResourceConfig(Mage_Api2_Model_Config_Resource $config)
    {
        $this->_resourceConfig = $config;

        return $this;
    }

    /**
     * Check permissions on specific resource in ACL. No information about roles must be used on this level.
     * ACL check must be performed in the same way for all API types
     */
    protected function _checkResourceAcl()
    {
        // TODO: Implement
        return $this;
    }

    /**
     * Set request object.
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Controller_FrontAbstract
     */
    public function setRequest(Mage_Api2_Model_Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve request object.
     *
     * @return Mage_Api2_Model_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object.
     *
     * @param Mage_Api2_Model_Response $response
     * @return Mage_Api2_Controller_FrontAbstract
     */
    public function setResponse(Mage_Api2_Model_Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Retrieve response object.
     *
     * @return Mage_Api2_Model_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Add exception to response.
     *
     * @param Exception $exception
     * @return Mage_Api2_Controller_FrontAbstract
     */
    protected function _addException(Exception $exception)
    {
        $response = $this->getResponse();
        $response->setException($exception);
        return $this;
    }
}
