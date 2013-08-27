<?php
/**
 * REST API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Rest_Request extends Mage_Webapi_Controller_Request
{
    /**
     * Character set which must be used in request.
     */
    const REQUEST_CHARSET = 'utf-8';

    /** @var string */
    protected $_serviceName;

    /** @var string */
    protected $_serviceType;

    /** @var string */
    protected $_serviceVersion;

    /**
     * @var Mage_Webapi_Controller_Rest_Request_InterpreterInterface
     */
    protected $_interpreter;

    /** @var array */
    protected $_bodyParams;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Webapi_Controller_Rest_Request_Interpreter_Factory */
    protected $_interpreterFactory;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Config $config
     * @param Mage_Webapi_Controller_Rest_Request_Interpreter_Factory $interpreterFactory
     * @param Mage_Webapi_Helper_Data $helper
     * @param string|null $uri
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Webapi_Controller_Rest_Request_Interpreter_Factory $interpreterFactory,
        Mage_Webapi_Helper_Data $helper,
        $uri = null
    ) {
        parent::__construct($config, Mage_Webapi_Controller_Rest::REQUEST_TYPE, $uri);
        $this->_helper = $helper;
        $this->_interpreterFactory = $interpreterFactory;
    }

    /**
     * Get request interpreter.
     *
     * @return Mage_Webapi_Controller_Rest_Request_InterpreterInterface
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
     * Retrieve current HTTP method.
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
     * Retrieve service type.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->_serviceName;
    }

    /**
     * Set service type.
     *
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->_serviceName = $serviceName;
    }

    /**
     * Retrieve action type.
     *
     * @return string|null
     */
    public function getServiceType()
    {
        return $this->_serviceType;
    }

    /**
     * Set service type.
     *
     * @param string $serviceType
     */
    public function setServiceType($serviceType)
    {
        $this->_serviceType = $serviceType;
    }

    /**
     * Retrieve action version.
     *
     * @return int
     */
    public function getServiceVersion()
    {
        if (!$this->_serviceVersion) {
            // TODO: Default version can be identified and returned here
            return 1;
        }
        return $this->_serviceVersion;
    }

    /**
     * Set service version.
     *
     * @param string|int $serviceVersion Version number either with prefix or without it
     * @throws Mage_Webapi_Exception
     * @return Mage_Webapi_Controller_Rest_Request
     */
    public function setServiceVersion($serviceVersion)
    {
        $versionPrefix = self::VERSION_NUMBER_PREFIX;
        if (preg_match("/^{$versionPrefix}?(\d+)$/i", $serviceVersion, $matches)) {
            $versionNumber = (int)$matches[1];
        } else {
            throw new Mage_Webapi_Exception(
                $this->_helper->__("Service version is not specified or invalid one is specified."),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
        $this->_serviceVersion = $versionNumber;
        return $this;
    }
}
