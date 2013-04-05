<?php
/**
 * REST API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_Rest extends Mage_Webapi_Controller_Request
{
    /**
     * Character set which must be used in request.
     */
    const REQUEST_CHARSET = 'utf-8';

    /**#@+
     * HTTP methods supported by REST.
     */
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    /**#@-*/

    /**#@+
     * Resource types.
     */
    const ACTION_TYPE_ITEM = 'item';
    const ACTION_TYPE_COLLECTION = 'collection';
    /**#@-*/

    /** @var string */
    protected $_serviceName;

    /** @var string */
    protected $_methodName;

    /** @var string */
    protected $_resourceVersion;

    /**
     * @var Mage_Webapi_Controller_Request_Rest_InterpreterInterface
     */
    protected $_interpreter;

    /** @var array */
    protected $_bodyParams;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Webapi_Controller_Request_Rest_Interpreter_Factory */
    protected $_interpreterFactory;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Request_Rest_Interpreter_Factory $interpreterFactory
     * @param Mage_Webapi_Helper_Data $helper
     * @param string|null $uri
     */
    public function __construct(
        Mage_Webapi_Controller_Request_Rest_Interpreter_Factory $interpreterFactory,
        Mage_Webapi_Helper_Data $helper,
        $uri = null
    ) {
        parent::__construct(Mage_Webapi_Controller_Front::API_TYPE_REST, $uri);
        $this->_helper = $helper;
        $this->_interpreterFactory = $interpreterFactory;
    }

    /**
     * Get request interpreter.
     *
     * @return Mage_Webapi_Controller_Request_Rest_InterpreterInterface
     */
    protected function _getInterpreter()
    {
        if (null === $this->_interpreter) {
            $this->_interpreter = $this->_interpreterFactory->get($this->getContentType());
        }
        return $this->_interpreter;
    }

    /**
     * Retrieve accept types understandable by requester in a form of array sorted by quality in descending order.
     *
     * @return array
     */
    public function getAcceptTypes()
    {
        $qualityToTypes = array();
        $orderedTypes = array();

        foreach (preg_split('/,\s*/', $this->getHeader('Accept')) as $definition) {
            $typeWithQ = explode(';', $definition);
            $mimeType = trim(array_shift($typeWithQ));

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
            throw new Mage_Webapi_Exception($this->_helper->__('Content-Type header is empty.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new Mage_Webapi_Exception($this->_helper->__('Content-Type header is invalid.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new Mage_Webapi_Exception($this->_helper->__('UTF-8 is the only supported charset.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        return $matches[1];
    }

    /**
     * Retrieve one of CRUD operations depending on HTTP method.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getHttpMethod()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new Mage_Webapi_Exception($this->_helper->__('Request method is invalid.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $this->getMethod();
    }

    /**
     * Retrieve resource type.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->_serviceName;
    }

    /**
     * Set resource type.
     *
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->_serviceName = $serviceName;
    }

    /**
     * Retrieve service method name.
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * Set service method name.
     *
     * @param string $methodName
     * @return Mage_Webapi_Controller_Request_Rest
     */
    public function setMethodName($methodName)
    {
        $this->_methodName = $methodName;
        return $this;
    }

    /**
     * Retrieve action version.
     *
     * @return int
     * @throws LogicException If resource version cannot be identified.
     */
    public function getResourceVersion()
    {
        if (!$this->_resourceVersion) {
            $this->setResourceVersion($this->getParam(Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION));
        }
        return $this->_resourceVersion;
    }

    /**
     * Set resource version.
     *
     * @param string|int $resourceVersion Version number either with prefix or without it
     * @throws Mage_Webapi_Exception
     * @return Mage_Webapi_Controller_Request_Rest
     */
    public function setResourceVersion($resourceVersion)
    {
        $versionPrefix = Mage_Core_Service_Config::VERSION_NUMBER_PREFIX;
        if (preg_match("/^{$versionPrefix}?(\d+)$/i", $resourceVersion, $matches)) {
            $versionNumber = (int)$matches[1];
        } else {
            throw new Mage_Webapi_Exception(
                $this->_helper->__("Resource version is not specified or invalid one is specified."),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
        $this->_resourceVersion = $versionNumber;
        return $this;
    }

    /**
     * Identify operation name according to HTTP request parameters.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getOperationName()
    {
        $operationName = $this->getServiceName() . ucfirst($this->getMethodName());
        return $operationName;
    }
}
