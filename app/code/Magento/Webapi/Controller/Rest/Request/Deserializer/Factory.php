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
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_deserializers;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $deserializers
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        array $deserializers = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_deserializers = $deserializers;
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
        if (empty($this->_deserializers)) {
            throw new LogicException('Request deserializer adapter is not set.');
        }
        foreach ($this->_deserializers as $deserializerMetadata) {
            $deserializerType = $deserializerMetadata['type'];
            if ($deserializerType == $contentType) {
                $deserializerClass = $deserializerMetadata['model'];
                break;
            }
        }

        if (!isset($deserializerClass) || empty($deserializerClass)) {
            throw new Magento_Webapi_Exception(
                __('Server cannot understand Content-Type HTTP header media type %1', $contentType)
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
