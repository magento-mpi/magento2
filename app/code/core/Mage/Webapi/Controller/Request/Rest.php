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
 * REST API Request
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Request_Rest extends Mage_Webapi_Controller_RequestAbstract
{
    /**
     * Character set which must be used in request
     */
    const REQUEST_CHARSET = 'utf-8';

    /** @var string */
    protected $_resourceName;

    /** @var string */
    protected $_resourceType;

    /**
     * Interpreter adapter.
     *
     * @var Mage_Webapi_Controller_Request_InterpreterInterface
     */
    protected $_interpreter;

    /** @var array */
    protected $_bodyParams;

    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * Initialize API type.
     *
     * @param string|null $uri
     * @param Mage_Core_Helper_Abstract|null $helper
     */
    public function __construct($uri = null, Mage_Core_Helper_Abstract $helper = null)
    {
        $this->_helper = $helper ? $helper : Mage::helper('Mage_Webapi_Helper_Data');
        $this->setApiType(Mage_Webapi_Controller_Front_Base::API_TYPE_REST);
        parent::__construct($uri);
    }

    /**
     * Get request interpreter.
     *
     * @return Mage_Webapi_Controller_Request_InterpreterInterface
     */
    protected function _getInterpreter()
    {
        if (null === $this->_interpreter) {
            $this->_interpreter = Mage_Webapi_Controller_Request_Interpreter::factory($this->getContentType());
        }
        return $this->_interpreter;
    }

    /**
     * Retrieve accept types understandable by requester in a form of array sorted by quality descending.
     *
     * @return array
     */
    public function getAcceptTypes()
    {
        $qualityToTypes = array();
        $orderedTypes   = array();

        foreach (preg_split('/,\s*/', $this->getHeader('Accept')) as $definition) {
            $typeWithQ = explode(';', $definition);
            $mimeType  = trim(array_shift($typeWithQ));

            // check MIME type validity
            if (!preg_match('~^([0-9a-z*+\-]+)(?:/([0-9a-z*+\-\.]+))?$~i', $mimeType)) {
                continue;
            }
            $quality = '1.0'; // default value for quality

            if ($typeWithQ) {
                $qAndValue = explode('=', $typeWithQ[0]);

                if (2 == count($qAndValue)) {
                    $quality = $qAndValue[1];
                }
            }
            $qualityToTypes[$quality][$mimeType] = true;
        }
        krsort($qualityToTypes);

        foreach ($qualityToTypes as $typeList) {
            $orderedTypes += $typeList;
        }
        return array_keys($orderedTypes);
    }

    /**
     * Fetch data from HTTP Request body.
     *
     * @return array
     */
    public function getBodyParams()
    {
        if (null == $this->_bodyParams) {
            $this->_bodyParams = $this->_getInterpreter()->interpret((string)$this->getRawBody());
        }
        return $this->_bodyParams;
    }

    /**
     * Get Content-Type of request.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getContentType()
    {
        $headerValue = $this->getHeader('Content-Type');

        if (!$headerValue) {
            throw new Mage_Webapi_Exception($this->_helper->__('Content-Type header is empty'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new Mage_Webapi_Exception($this->_helper->__('Invalid Content-Type header'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new Mage_Webapi_Exception($this->_helper->__('UTF-8 is the only supported charset'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        return $matches[1];
    }

    /**
     * Retrieve one of CRUD operation dependent on HTTP method.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getHttpMethod()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new Mage_Webapi_Exception($this->_helper->__('Invalid request method'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        // Map HTTP methods to classic CRUD verbs
        $operationByMethod = array(
            'GET'    => Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_GET,
            'POST'   => Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_CREATE,
            'PUT'    => Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_UPDATE,
            'DELETE' => Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_DELETE
        );

        return $operationByMethod[$this->getMethod()];
    }

    /**
     * Retrieve resource type.
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * Set resource type.
     *
     * @param string $resourceName
     */
    public function setResourceName($resourceName)
    {
        $this->_resourceName = $resourceName;
    }

    /**
     * Retrieve action type.
     *
     * @return string|null
     */
    public function getResourceType()
    {
        return $this->_resourceType;
    }

    /**
     * Set resource type.
     *
     * @param $resourceType
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
    }

    /**
     * Identify versions of modules that should be used for API configuration file generation.
     *
     * @return array
     * @throws Mage_Webapi_Exception when header value is invalid
     */
    public function getRequestedResources()
    {
        $versionHeader = $this->getHeader('Modules');
        /**
         * Match a 'Mage_Customer=v1' and 'Enterprise_Customer=v2' from the following Modules header value:
         * Modules='Mage_Customer=v1;Enterprise_Customer=v2'
         */
        preg_match_all('/\w+\=(v|V)\d+/', $versionHeader, $moduleMatches);
        $requestedModules = array();
        foreach ($moduleMatches[0] as $moduleVersion) {
            $moduleVersion = explode('=', $moduleVersion);
            $requestedModules[reset($moduleVersion)] = end($moduleVersion);
        }
        if (empty($requestedModules) || !is_array($requestedModules) || empty($requestedModules)) {
            $message = $this->_helper->__('Invalid "Modules" header value. Example: ')
                . "Modules: Mage_Customer=v1;Mage_Catalog=v1;\n"
                // TODO: change documentation link
                . $this->_helper->__('See documentation: https://wiki.corp.x.com/display/APIA/New+API+module+architecture#NewAPImodulearchitecture-Resourcesversioning');

            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $requestedModules;
    }

    /**
     * Check if the array in the request body is an associative one.
     *
     * It is required for definition of the dynamic action type (multi or single).
     *
     * @return bool
     */
    public function isAssocArrayInRequestBody()
    {
        $params = $this->getBodyParams();
        if (count($params)) {
            $keys = array_keys($params);
            return !is_numeric($keys[0]);
        }
        return false;
    }
}
