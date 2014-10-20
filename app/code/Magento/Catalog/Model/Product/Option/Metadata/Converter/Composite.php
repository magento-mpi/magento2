<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Metadata\Converter;

use \Magento\Catalog\Model\Product\Option\Metadata\ConverterInterface;

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
    public function convert(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option)
    {
        $type = $option->getType();
        $converter = isset($this->converters[$type]) ? $this->converters[$type] : $this->converters['default'];
        return $converter->convert($option);
    }
}
