<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity;

/**
 * @codeCoverageIgnore
 */
class ConverterPool
{
    /**
     * @var ConverterInterface[]
     */
    protected $converters;

    /**
     * @var string
     */
    protected $defaultConverterCode = 'default';

    /**
     * @param  ConverterInterface[] $converters
     */
    public function __construct(array $converters)
    {
        $this->converters = $converters;
    }

    /**
     * Get converter by link type
     *
     * @param string $linkType
     * @return ConverterInterface
     */
    public function getConverter($linkType)
    {
        return isset($this->converters[$linkType])
            ? $this->converters[$linkType]
            : $this->converters[$this->defaultConverterCode];
    }
}
