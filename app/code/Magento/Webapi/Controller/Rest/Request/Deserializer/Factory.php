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
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_deserializers;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array $deserializers
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        array $deserializers = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_deserializers = $deserializers;
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
        if (empty($this->_deserializers)) {
            throw new \LogicException('Request deserializer adapter is not set.');
        }
        foreach ($this->_deserializers as $deserializerMetadata) {
            $deserializerType = $deserializerMetadata['type'];
            if ($deserializerType == $contentType) {
                $deserializerClass = $deserializerMetadata['model'];
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
