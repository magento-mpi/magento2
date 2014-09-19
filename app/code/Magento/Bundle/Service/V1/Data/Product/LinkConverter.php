<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Data\Product;

use Magento\Catalog\Model\Product;

/**
 * @codeCoverageIgnore
 */
class LinkConverter
{
    /**
     * @var LinkBuilder
     */
    private $builder;

    /**
     * @param LinkBuilder $builder
     */
    public function __construct(LinkBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Product $product
     * @param Product $bundle
     * @return Link
     */
    public function createDataFromModel(Product $product, Product $bundle)
    {
        $selectionPriceType = $selectionPrice = null;

        /** @var \Magento\Bundle\Model\Selection $product */
        if ($bundle->getPriceType()) {
            $selectionPriceType = $product->getSelectionPriceType();
            $selectionPrice = $product->getSelectionPriceValue();
        }

        $this->builder->populateWithArray($product->getData())
            ->setDefault($product->getIsDefault())
            ->setQty($product->getSelectionQty())
            ->setDefined($product->getSelectionCanChangeQty())
            ->setPrice($selectionPrice)
            ->setPriceType($selectionPriceType);
        return $this->builder->create();
    }
}
