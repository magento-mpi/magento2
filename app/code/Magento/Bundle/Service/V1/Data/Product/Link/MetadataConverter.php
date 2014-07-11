<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Data\Product\Link;


use Magento\Catalog\Model\Product;

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
     * @param Product $product
     * @return Metadata
     */
    public function createDataFromModel(Product $product)
    {
        return $this->builder->populateWithArray($product->getData())
            ->setDefault($product->getIsDefault())
            ->setQty($product->getSelectionQty())
            ->setDefined($product->getSelectionCanChangeQty())
            ->create();
    }
}
