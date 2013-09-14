<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Webservices server handler WSI
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Server_Handler_Soap_Wsi extends Magento_Api_Model_Server_HandlerAbstract
{
    protected $_resourceSuffix = '_V2';

    /**
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(Magento_Core_Model_Logger $logger)
    {
        parent::__construct($logger);
    }

    /**
     * Interceptor for all interfaces
     *
     * @param string $function
     * @param array $args
     */

    public function __call($function, $args)
    {
        $args = $args[0];

        /** @var Magento_Api_Helper_Data */
        $helper = Mage::helper('Magento_Api_Helper_Data');

        $helper->wsiArrayUnpacker($args);
        $args = get_object_vars($args);

        if (isset($args['sessionId'])) {
            $sessionId = $args['sessionId'];
            unset($args['sessionId']);
        } else {
            // Was left for backward compatibility.
            $sessionId = array_shift($args);
        }

        $apiKey = '';
        $nodes = Mage::getSingleton('Magento_Api_Model_Config')->getNode('v2/resources_function_prefix')->children();
        foreach ($nodes as $resource => $prefix) {
            $prefix = $prefix->asArray();
            if (false !== strpos($function, $prefix)) {
                $method = substr($function, strlen($prefix));
                $apiKey = $resource . '.' . strtolower($method[0]) . substr($method, 1);
            }
        }

        list($modelName, $methodName) = $this->_getResourceName($apiKey);
        $methodParams = $this->getMethodParams($modelName, $methodName);

        $args = $this->prepareArgs($methodParams, $args);

        $res = $this->_call($sessionId, $apiKey, $args);

        $obj = $helper->wsiArrayPacker($res);
        $stdObj = new stdClass();
        $stdObj->result = $obj;

        return $stdObj;
    }

    /**
     * Login user and Retrieve session id
     *
     * @param string $username
     * @param string $apiKey
     * @return string
     */
    public function login($username, $apiKey = null)
    {
        if (is_object($username)) {
            $apiKey = $username->apiKey;
            $username = $username->username;
        }

        $stdObject = new stdClass();
        $stdObject->result = parent::login($username, $apiKey);
        return $stdObject;
    }

    /**
     * Return called class and method names
     *
     * @param String $apiPath
     * @return Array
     */
    protected function _getResourceName($apiPath)
    {

        list($resourceName, $methodName) = explode('.', $apiPath);

        if (empty($resourceName) || empty($methodName)) {
            return $this->_fault('resource_path_invalid');
        }

        $resourcesAlias = $this->_getConfig()->getResourcesAlias();
        $resources = $this->_getConfig()->getResources();
        if (isset($resourcesAlias->$resourceName)) {
            $resourceName = (string)$resourcesAlias->$resourceName;
        }

        $methodInfo = $resources->$resourceName->methods->$methodName;

        $modelName = $this->_prepareResourceModelName((string)$resources->$resourceName->model);

        $modelClass = $modelName;

        $method = (isset($methodInfo->method) ? (string)$methodInfo->method : $methodName);

        return array($modelClass, $method);
    }

    /**
     * Return an array of parameters for the callable method.
     *
     * @param String $modelName
     * @param String $methodName
     * @return Array of ReflectionParameter
     */
    public function getMethodParams($modelName, $methodName)
    {

        $method = new ReflectionMethod($modelName, $methodName);

        return $method->getParameters();
    }

    /**
     * Prepares arguments for the method calling. Sort in correct order, set default values for omitted parameters.
     *
     * @param Array $params
     * @param Array $args
     * @return Array
     */
    public function prepareArgs($params, $args)
    {

        $callArgs = array();

        /** @var $parameter ReflectionParameter */
        foreach ($params AS $parameter) {
            $pName = $parameter->getName();
            if (isset($args[$pName])) {
                $callArgs[$pName] = $args[$pName];
            } else {
                if ($parameter->isOptional()) {
                    $callArgs[$pName] = $parameter->getDefaultValue();
                } else {
                    $this->_logger->logException(new Exception("Required parameter \"$pName\" is missing.", 0));
                    $this->_fault('invalid_request_param');
                }
            }
        }
        return $callArgs;
    }

    /**
     * End web service session
     *
     * @param object $request
     * @return stdClass
     */
    public function endSession($request)
    {
        $stdObject = new stdClass();
        $stdObject->result = parent::endSession($request->sessionId);
        return $stdObject;
    }
}
