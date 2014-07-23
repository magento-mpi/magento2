<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Option;

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;

/**
 * @codeCoverageIgnore
 */
class MetadataConverter
{
    /**
     * @var MetadataBuilder
     */
    private $builder;

    /**
     * @param MetadataBuilder $builder
     */
    public function __construct(MetadataBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Option $option
     * @param Product $product
     * @return Metadata
     */
    public function createDataFromModel(Option $option, Product $product)
    {
        $this->builder->populateWithArray($option->getData())
            ->setId($option->getId())
            ->setTitle($option->getDefaultTitle())
            ->setSku($product->getSku());
        return $this->builder->create();
    }
}
