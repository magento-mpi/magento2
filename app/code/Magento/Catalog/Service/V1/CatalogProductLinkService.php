<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Catalog\Service\V1\Data\CatalogProductLinkBuilder as Builder;
use \Magento\Catalog\Service\V1\Data\CatalogProductLink;
use \Magento\Catalog\Service\V1\Data\CatalogProductLinkEntity;

class CatalogProductLinkService implements CatalogProductLinkServiceInterface
{
    /**
     * @var LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var Data\CatalogProductLinkEntityBuilder
     */
    protected $productEntityBuilder;

    /**
     * @var Data\Entity\CollectionProvider
     */
    protected $entityCollectionProvider;

    /**
     * @param LinkTypeProvider $linkTypeProvider
     * @param Data\CatalogProductLinkEntityBuilder $productEntityBuilder
     * @param Builder $builder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Data\Entity\CollectionProvider $entityCollectionProvider
     */
    public function __construct(
        LinkTypeProvider $linkTypeProvider,
        Builder $builder,
        Data\CatalogProductLinkEntityBuilder $productEntityBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Data\Entity\CollectionProvider $entityCollectionProvider
    ) {
        $this->linkTypeProvider = $linkTypeProvider;
        $this->builder = $builder;
        $this->productFactory = $productFactory;
        $this->productEntityBuilder = $productEntityBuilder;
        $this->entityCollectionProvider = $entityCollectionProvider;
    }

    /**
     * Provide the list of product link types
     *
     * @return \Magento\Catalog\Service\V1\Data\CatalogProductLink[]
     */
    public function getProductLinkTypes()
    {
        $output = [];
        foreach ($this->linkTypeProvider->getLinkTypes() as $type => $typeCode) {
            $data = [CatalogProductLink::TYPE => $type, CatalogProductLink::CODE => $typeCode];
            $output[] = $this->builder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }

    /**
     * Provide the list of linked products for a specific product
     *
     * @param int $productId
     * @param int $type
     * @return \Magento\Catalog\Service\V1\Data\CatalogProductLinkEntity[]
     */
    public function getLinkedProducts($productId, $type)
    {
        $output = [];
        $product = $this->productFactory->create();
        $product->load($productId);
        foreach ($this->entityCollectionProvider->getCollection($product, $type) as $item) {
            /** @var \Magento\Catalog\Model\Product $item */
            $data = [
                CatalogProductLinkEntity::ID => $item->getId(),
                CatalogProductLinkEntity::TYPE => $item->getTypeId(),
                CatalogProductLinkEntity::ATTRIBUTE_SET_ID => $item->getAttributeSetId(),
                CatalogProductLinkEntity::SKU => $item->getSku(),
                CatalogProductLinkEntity::POSITION => $item->getPosition()
            ];
            $output[] = $this->productEntityBuilder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }
}
