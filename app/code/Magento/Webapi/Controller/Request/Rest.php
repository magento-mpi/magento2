<?php
/**
 * REST API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request;

class Rest extends \Magento\Webapi\Controller\Request
{
    /**
     * Character set which must be used in request.
     */
    const REQUEST_CHARSET = 'utf-8';

    /**#@+
     * HTTP methods supported by REST.
     */
    const HTTP_METHOD_CREATE = 'create';
    const HTTP_METHOD_GET = 'get';
    const HTTP_METHOD_UPDATE = 'update';
    const HTTP_METHOD_DELETE = 'delete';
    /**#@-*/

    /**#@+
     * Resource types.
     */
    const ACTION_TYPE_ITEM = 'item';
    const ACTION_TYPE_COLLECTION = 'collection';
    /**#@-*/

    /** @var string */
    protected $_resourceName;

    /** @var string */
    protected $_resourceType;

    /** @var string */
    protected $_resourceVersion;

    /**
     * @var \Magento\Webapi\Controller\Request\Rest\InterpreterInterface
     */
    protected $_interpreter;

    /** @var array */
    protected $_bodyParams;

    /** @var \Magento\Webapi\Controller\Request\Rest\Interpreter\Factory */
    protected $_interpreterFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Request\Rest\Interpreter\Factory $interpreterFactory
     * @param string|null $uri
     */
    public function __construct(
        \Magento\Webapi\Controller\Request\Rest\Interpreter\Factory $interpreterFactory,
        $uri = null
    ) {
        parent::__construct(\Magento\Webapi\Controller\Front::API_TYPE_REST, $uri);
        $this->_interpreterFactory = $interpreterFactory;
    }

    /**
     * Get request interpreter.
     *
     * @return \Magento\Webapi\Controller\Request\Rest\InterpreterInterface
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
     * @throws \Magento\Webapi\Exception
     */
    public function getContentType()
    {
        $headerValue = $this->getHeader('Content-Type');

        if (!$headerValue) {
            throw new \Magento\Webapi\Exception(__('Content-Type header is empty.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new \Magento\Webapi\Exception(__('Content-Type header is invalid.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new \Magento\Webapi\Exception(__('UTF-8 is the only supported charset.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        }

        return $matches[1];
    }

    /**
     * Retrieve one of CRUD operations depending on HTTP method.
     *
     * @return string
     * @throws \Magento\Webapi\Exception
     */
    public function getHttpMethod()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new \Magento\Webapi\Exception(__('Request method is invalid.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        }
        // Map HTTP methods to classic CRUD verbs
        $operationByMethod = array(
            'GET' => self::HTTP_METHOD_GET,
            'POST' => self::HTTP_METHOD_CREATE,
            'PUT' => self::HTTP_METHOD_UPDATE,
            'DELETE' => self::HTTP_METHOD_DELETE
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
     * @param string $resourceType
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
    }

    /**
     * Retrieve action version.
     *
     * @return int
     * @throws \LogicException If resource version cannot be identified.
     */
    public function getResourceVersion()
    {
        if (!$this->_resourceVersion) {
            $this->setResourceVersion($this->getParam(\Magento\Webapi\Controller\Router\Route\Rest::PARAM_VERSION));
        }
        return $this->_resourceVersion;
    }

    /**
     * Set resource version.
     *
     * @param string|int $resourceVersion Version number either with prefix or without it
     * @throws \Magento\Webapi\Exception
     * @return \Magento\Webapi\Controller\Request\Rest
     */
    public function setResourceVersion($resourceVersion)
    {
        $versionPrefix = \Magento\Webapi\Model\ConfigAbstract::VERSION_NUMBER_PREFIX;
        if (preg_match("/^{$versionPrefix}?(\d+)$/i", $resourceVersion, $matches)) {
            $versionNumber = (int)$matches[1];
        } else {
            throw new \Magento\Webapi\Exception(
                __("Resource version is not specified or invalid one is specified."),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }
        $this->_resourceVersion = $versionNumber;
        return $this;
    }

    /**
     * Identify operation name according to HTTP request parameters.
     *
     * @return string
     * @throws \Magento\Webapi\Exception
     */
    public function getOperationName()
    {
        $restMethodsMap = array(
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_CREATE =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_CREATE,
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_GET =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_LIST,
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_UPDATE =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_UPDATE,
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_DELETE =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_DELETE,
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_GET =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_GET,
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_UPDATE =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_UPDATE,
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_DELETE =>
                \Magento\Webapi\Controller\ActionAbstract::METHOD_DELETE,
        );
        $httpMethod = $this->getHttpMethod();
        $resourceType = $this->getResourceType();
        if (!isset($restMethodsMap[$resourceType . $httpMethod])) {
            throw new \Magento\Webapi\Exception(__('Requested method does not exist.'),
                \Magento\Webapi\Exception::HTTP_NOT_FOUND);
        }
        $methodName = $restMethodsMap[$resourceType . $httpMethod];
        if ($methodName == self::HTTP_METHOD_CREATE) {
            /** If request is numeric array, multi create operation must be used. */
            $params = $this->getBodyParams();
            if (count($params)) {
                $keys = array_keys($params);
                if (is_numeric($keys[0])) {
                    $methodName = \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_CREATE;
                }
            }
        }
        $operationName = $this->getResourceName() . ucfirst($methodName);
        return $operationName;
    }

    /**
     * Identify resource type by operation name.
     *
     * @param string $operation
     * @return string 'collection' or 'item'
     * @throws \InvalidArgumentException When method does not match the list of allowed methods
     */
    public static function getActionTypeByOperation($operation)
    {
        $actionTypeMap = array(
            \Magento\Webapi\Controller\ActionAbstract::METHOD_CREATE => self::ACTION_TYPE_COLLECTION,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_CREATE => self::ACTION_TYPE_COLLECTION,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_GET => self::ACTION_TYPE_ITEM,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_LIST => self::ACTION_TYPE_COLLECTION,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_UPDATE => self::ACTION_TYPE_ITEM,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_UPDATE => self::ACTION_TYPE_COLLECTION,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_DELETE => self::ACTION_TYPE_ITEM,
            \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_DELETE => self::ACTION_TYPE_COLLECTION,
        );
        if (!isset($actionTypeMap[$operation])) {
            throw new \InvalidArgumentException(sprintf('The "%s" method is not a valid resource method.', $operation));
        }
        return $actionTypeMap[$operation];
    }
}
