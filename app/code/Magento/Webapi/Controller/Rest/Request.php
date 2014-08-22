<?php
/**
 * REST API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Rest;

class Request extends \Magento\Webapi\Controller\Request
{
    /**
     * Character set which must be used in request.
     */
    const REQUEST_CHARSET = 'utf-8';

    /** @var string */
    protected $_serviceName;

    /** @var string */
    protected $_serviceType;

    /** @var \Magento\Webapi\Controller\Rest\Request\DeserializerInterface */
    protected $_deserializer;

    /** @var array */
    protected $_bodyParams;

    /** @var \Magento\Webapi\Controller\Rest\Request\Deserializer\Factory */
    protected $_deserializerFactory;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Framework\Config\ScopeInterface $configScope
     * @param \Magento\Framework\Stdlib\CookieManager $cookieManager
     * @param \Magento\Webapi\Controller\Rest\Request\Deserializer\Factory $deserializerFactory
     * @param null|string $uri
     */
    public function __construct(
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Stdlib\CookieManager $cookieManager,
        \Magento\Webapi\Controller\Rest\Request\Deserializer\Factory $deserializerFactory,
        $uri = null
    ) {
        parent::__construct($areaList, $configScope, $cookieManager, $uri);
        $this->_deserializerFactory = $deserializerFactory;
    }

    /**
     * Get request deserializer.
     *
     * @return \Magento\Webapi\Controller\Rest\Request\DeserializerInterface
     */
    protected function _getDeserializer()
    {
        if (null === $this->_deserializer) {
            $this->_deserializer = $this->_deserializerFactory->get($this->getContentType());
        }
        return $this->_deserializer;
    }

    /**
     * Retrieve accept types understandable by requester in a form of array sorted by quality in descending order.
     *
     * @return string[]
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
            $quality = '1.0';
            // default value for quality

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
            $this->_bodyParams = (array)$this->_getDeserializer()->deserialize((string)$this->getRawBody());
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
            throw new \Magento\Webapi\Exception(__('Content-Type header is empty.'));
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new \Magento\Webapi\Exception(__('Content-Type header is invalid.'));
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new \Magento\Webapi\Exception(__('UTF-8 is the only supported charset.'));
        }

        return $matches[1];
    }

    /**
     * Retrieve current HTTP method.
     *
     * @return string
     * @throws \Magento\Webapi\Exception
     */
    public function getHttpMethod()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new \Magento\Webapi\Exception(__('Request method is invalid.'));
        }
        return $this->getMethod();
    }

    /**
     * Fetch and return parameter data from the request.
     *
     * @return array
     */
    public function getRequestData()
    {
        $requestBody = array();

        $httpMethod = $this->getHttpMethod();
        if ($httpMethod == \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST ||
            $httpMethod == \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
        ) {
            $requestBody = $this->getBodyParams();
        }

        return array_merge($requestBody, $this->getParams());
    }
}
