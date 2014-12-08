<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\MetadataBuilder;

class DefaultReader implements ReaderInterface
{
    /**
     * @var MetadataBuilder
     */
    protected $valueBuilder;

    /**
     * @param MetadataBuilder $valueBuilder
     */
    public function __construct(MetadataBuilder $valueBuilder)
    {
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function read(\Magento\Catalog\Model\Product\Option $option)
    {
        $fields = [
            Metadata::PRICE => $option->getPrice(),
            Metadata::PRICE_TYPE => $option->getPriceType(),
            Metadata::SKU => $option->getSku(),
        ];
        $fields = array_merge($fields, $this->getCustomAttributes($option));
        $value = $this->valueBuilder->populateWithArray($fields)->create();
        return [$value];
    }

    /**
     * Get custom attributes
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getCustomAttributes(\Magento\Catalog\Model\Product\Option $option)
    {
        return [];
    }
}
