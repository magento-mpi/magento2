<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;

use Magento\Framework\Api\AttributeValueBuilder;

/**
 * @codeCoverageIgnore
 */
class MetadataBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * @var string[]
     */
    protected $customAttributeCodes = [
        Metadata::SORT_ORDER,
        Metadata::TITLE,
        Metadata::FILE_EXTENSION,
        Metadata::IMAGE_SIZE_X,
        Metadata::IMAGE_SIZE_Y,
        Metadata::MAX_CHARACTERS,
        Metadata::OPTION_TYPE_ID
    ];

    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param \Magento\Framework\Service\Config\MetadataConfig $metadataService
     * @param array $customAttributeCodes
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        \Magento\Framework\Service\Config\MetadataConfig $metadataService,
        array $customAttributeCodes = array()
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->customAttributeCodes = array_merge($this->customAttributeCodes, $customAttributeCodes);
    }

    /**
     * Set price
     *
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->_set(Metadata::PRICE, $value);
    }

    /**
     * Set price type
     *
     * @param string $value
     * @return $this
     */
    public function setPriceType($value)
    {
        return $this->_set(Metadata::PRICE_TYPE, $value);
    }

    /**
     * Set Sku
     *
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(Metadata::SKU, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesCodes()
    {
        return array_merge($this->customAttributeCodes, parent::getCustomAttributesCodes());
    }
}
