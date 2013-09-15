<?php
/**
 * Factory of REST request deserializers.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Rest\Request\Deserializer;

class Factory
{
    /**
     * Request deserializer adapters.
     */
    const XML_PATH_WEBAPI_REQUEST_DESERIALIZERS = 'global/webapi/rest/request/deserializers';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /** @var \Magento\Core\Model\Config */
    protected $_applicationConfig;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Config $applicationConfig
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Config $applicationConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Retrieve proper deserializer for the specified content type.
     *
     * @param string $contentType
     * @return \Magento\Webapi\Controller\Rest\Request\DeserializerInterface
     * @throws \LogicException|\Magento\Webapi\Exception
     */
    public function get($contentType)
    {
        $deserializers = (array)$this->_applicationConfig->getNode(self::XML_PATH_WEBAPI_REQUEST_DESERIALIZERS);
        if (empty($deserializers) || !is_array($deserializers)) {
            throw new \LogicException('Request deserializer adapter is not set.');
        }
        foreach ($deserializers as $deserializer) {
            $deserializerType = (string)$deserializer->type;
            if ($deserializerType == $contentType) {
                $deserializerClass = (string)$deserializer->model;
                break;
            }
        }

        if (!isset($deserializerClass) || empty($deserializerClass)) {
            throw new \Magento\Webapi\Exception(
                __('Server cannot understand Content-Type HTTP header media type %1', $contentType)
            );
        }

        $deserializer = $this->_objectManager->get($deserializerClass);
        if (!$deserializer instanceof \Magento\Webapi\Controller\Rest\Request\DeserializerInterface) {
            throw new \LogicException(
                'The deserializer must implement "Magento\Webapi\Controller\Rest\Request\DeserializerInterface".');
        }
        return $deserializer;
    }
}
