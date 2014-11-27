<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Product;

class LinksList
{
    /**
     * @var \Magento\Bundle\Api\Data\LinkDataBuilder
     */
    protected $linkBuilder;

    /**
     * @param \Magento\Bundle\Api\Data\LinkDataBuilder $linkBuilder
     */
    public function __construct(
        \Magento\Bundle\Api\Data\LinkDataBuilder $linkBuilder
    ) {
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $optionId
     * @return \Magento\Bundle\Api\Data\LinkInterface[]
     */
    public function getItems(\Magento\Catalog\Api\Data\ProductInterface $product, $optionId)
    {
        /** @var \Magento\Bundle\Model\Product\Type $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter(
            $product->getStoreId(),
            $product
        );
        $selectionCollection = $productTypeInstance->getSelectionsCollection(
            [ $optionId ],
            $product
        );

        $productLinks = [];
        /** @var \Magento\Catalog\Model\Product $selection */
        foreach ($selectionCollection as $selection) {
            $selectionPriceType = $product->getPriceType() ? $selection->getSelectionPriceType() : null;
            $selectionPrice = $product->getPriceType() ? $selection->getSelectionPriceValue() : null;

            $productLinks[] = $this->linkBuilder->populateWithArray($selection->getData())
                ->setIsDefault($selection->getIsDefault())
                ->setQty($selection->getSelectionQty())
                ->setIsDefined($selection->getSelectionCanChangeQty())
                ->setPrice($selectionPrice)
                ->setPriceType($selectionPriceType)
                ->create();
        }
        return $productLinks;
    }
}
