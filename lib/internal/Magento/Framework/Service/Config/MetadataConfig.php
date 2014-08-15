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
     * Initialize dependencies.
     *
     * @param ServiceConfigReader $serviceConfigReader
     * @param AttributeMetadataBuilderInterface|null $attributeMetadataBuilder
     */
    public function __construct(
        ServiceConfigReader $serviceConfigReader,
        AttributeMetadataBuilderInterface $attributeMetadataBuilder = null
    ) {
        $this->serviceConfigReader = $serviceConfigReader;
        /*
         * Default preference set in app/etc/di as Magento\Framework\Service\Data\AttributeMetadataBuilder
         */
        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        $attributes = [];
        if (!is_null($this->attributeMetadataBuilder) && !is_null($dataObjectClassName)) {
            /**
             * Attribute metadata builder and data object class name are expected to be configured
             * via DI using virtual types. If configuration is missing, empty array should be returned.
             */
            $allAttributes = $this->serviceConfigReader->read();
            if (isset($allAttributes[$dataObjectClassName])
                && is_array($allAttributes[$dataObjectClassName])
            ) {
                $attributeCodes = array_keys($allAttributes[$dataObjectClassName]);
                foreach ($attributeCodes as $attributeCode) {
                    $this->attributeMetadataBuilder->setAttributeCode($attributeCode);
                    $attributes[$attributeCode] = $this->attributeMetadataBuilder->create();
                }
            }
        }
        return $attributes;
    }
}
