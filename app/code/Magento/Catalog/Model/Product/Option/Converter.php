<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option;

use \Magento\Catalog\Model\Product\Option\Metadata\ConverterInterface as MetadataConverter;

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
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return array
     */
    public function convert(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option)
    {
        $output = [
            'option_id' => $option->getOptionId(),
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'sort_order' => $option->getSortOrder(),
            'is_require' => $option->getIsRequire()
        ];
        $output = array_merge($output, $this->metadataConverter->convert($option));
        return $output;
    }
}
