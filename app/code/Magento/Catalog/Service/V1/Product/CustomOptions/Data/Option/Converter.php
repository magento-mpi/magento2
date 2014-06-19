<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ConverterInterface as ValueConverter;

class Converter
{
    /**
     * @var ValueConverter
     */
    protected $valueConverter;

    /**
     * @param ValueConverter $valueConverter
     */
    public function __construct(ValueConverter $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * Convert data object to array
     *
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return array
     */
    public function covert(\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option)
    {
        $output = [
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'sort_order' => $option->getSortOrder(),
            'is_require' => $option->getIsRequire()
        ];
        $output = array_merge($output, $this->valueConverter->convert($option));
        return $output;
    }
}
