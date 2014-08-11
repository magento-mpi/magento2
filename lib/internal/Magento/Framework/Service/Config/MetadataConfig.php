<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Config;

use Magento\Framework\Service\Data\Eav\MetadataServiceInterface;
use Magento\Framework\Service\Config\Reader as ServiceConfigReader;
use Magento\Framework\Service\Data\AttributeMetadataBuilderInterface;

/**
 * Class which allows to get a metadata of the attributes declared in a config.
 */
class MetadataConfig implements MetadataServiceInterface
{
    /**
     * @var ServiceConfigReader
     */
    private $serviceConfigReader;

    /**
     * @var AttributeMetadataBuilderInterface
     */
    private $attributeMetadataBuilder;

    /**
     * @var string
     */
    private $dataObjectClassName;

    /**
     * Initialize dependencies.
     *
     * @param ServiceConfigReader $serviceConfigReader
     * @param AttributeMetadataBuilderInterface|null $attributeMetadataBuilder
     * @param string|null $dataObjectClassName
     */
    public function __construct(
        ServiceConfigReader $serviceConfigReader,
        AttributeMetadataBuilderInterface $attributeMetadataBuilder = null,
        $dataObjectClassName = null
    ) {
        $this->serviceConfigReader = $serviceConfigReader;
        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
        $this->dataObjectClassName = $dataObjectClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata()
    {
        $attributes = [];
        if (!is_null($this->attributeMetadataBuilder) && !is_null($this->dataObjectClassName)) {
            /**
             * Attribute metadata builder and data object class name are expected to be configured
             * via DI using virtual types. If configuration is missing, empty array should be returned.
             */
            $allAttributes = $this->serviceConfigReader->read();
            if (isset($allAttributes[$this->dataObjectClassName]) && is_array(
                    $allAttributes[$this->dataObjectClassName]
                )
            ) {
                $attributeCodes = array_keys($allAttributes[$this->dataObjectClassName]);
                foreach ($attributeCodes as $attributeCode) {
                    $this->attributeMetadataBuilder->setAttributeCode($attributeCode);
                    $attributes[$attributeCode] = $this->attributeMetadataBuilder->create();
                }
            }
        }
        return $attributes;
    }
}
