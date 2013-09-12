<?php
/**
 * Factory of REST request deserializers.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Request_Deserializer_Factory
{
    /**
     * Request deserializer adapters.
     */
    const XML_PATH_WEBAPI_REQUEST_DESERIALIZERS = 'global/webapi/rest/request/deserializers';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Magento_Core_Model_Config */
    protected $_applicationConfig;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Config $applicationConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Retrieve proper deserializer for the specified content type.
     *
     * @param string $contentType
     * @return Magento_Webapi_Controller_Rest_Request_DeserializerInterface
     * @throws LogicException|Magento_Webapi_Exception
     */
    public function get($contentType)
    {
        $deserializers = (array)$this->_applicationConfig->getNode(self::XML_PATH_WEBAPI_REQUEST_DESERIALIZERS);
        if (empty($deserializers) || !is_array($deserializers)) {
            throw new LogicException('Request deserializer adapter is not set.');
        }
        foreach ($deserializers as $deserializer) {
            $deserializerType = (string)$deserializer->type;
            if ($deserializerType == $contentType) {
                $deserializerClass = (string)$deserializer->model;
                break;
            }
        }

        if (!isset($deserializerClass) || empty($deserializerClass)) {
            throw new Magento_Webapi_Exception(
                __('Server cannot understand Content-Type HTTP header media type %1', $contentType),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }

        $deserializer = $this->_objectManager->get($deserializerClass);
        if (!$deserializer instanceof Magento_Webapi_Controller_Rest_Request_DeserializerInterface) {
            throw new LogicException(
                'The deserializer must implement "Magento_Webapi_Controller_Rest_Request_DeserializerInterface".');
        }
        return $deserializer;
    }
}
