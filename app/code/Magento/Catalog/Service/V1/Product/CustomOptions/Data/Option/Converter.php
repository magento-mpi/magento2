<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface as MetadataConverter;

class Converter
{
    /**
     * @var MetadataConverter
     */
    protected $metadataConverter;

    /**
     * @param MetadataConverter $valueConverter
     */
    public function __construct(MetadataConverter $valueConverter)
    {
        $this->metadataConverter = $valueConverter;
    }

    /**
     * Convert data object to array
     *
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return array
     */
    public function convert(\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option)
    {
        $output = [
            'option_id' => $option->getOptionId(),
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'sort_order' => $option->getSortOrder(),
            'is_require' => $option->getIsRequire(),
        ];
        $output = array_merge($output, $this->metadataConverter->convert($option));
        return $output;
    }
}
