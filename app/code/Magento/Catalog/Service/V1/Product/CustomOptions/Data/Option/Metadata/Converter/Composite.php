<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Converter;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface;

class Composite implements ConverterInterface
{
    /**
     * @var ConverterInterface[]
     */
    protected $converters;

    /**
     * @param ConverterInterface[] $converters
     */
    public function __construct(array $converters)
    {
        $this->converters = $converters;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option)
    {
        $type = $option->getType();
        $converter = isset($this->converters[$type]) ? $this->converters[$type] : $this->converters['default'];
        return $converter->convert($option);
    }
}
