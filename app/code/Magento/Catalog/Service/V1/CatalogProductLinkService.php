<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Catalog\Service\V1\Data\LinkTypeEntityBuilder as Builder;
use \Magento\Catalog\Service\V1\Data\LinkTypeEntity;
use \Magento\Catalog\Service\V1\Data\LinkedProductEntity;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Logger;

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
     * @var Data\LinkedProductEntityBuilder
     */
    protected $productEntityBuilder;

    /**
     * @var Data\LinkedProductEntity\CollectionProvider
     */
    protected $entityCollectionProvider;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $linkFactory;

    /**
     * @var Data\LinkAttributeEntityBuilder
     */
    protected $linkAttributeBuilder;

    /**
     * @param LinkTypeProvider $linkTypeProvider
     * @param Builder $builder
     * @param Data\LinkedProductEntityBuilder $productEntityBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Data\LinkedProductEntity\CollectionProvider $entityCollectionProvider
     * @param Data\LinkAttributeEntityBuilder $linkAttributeBuilder
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param Logger $logger
     */
    public function __construct(
        LinkTypeProvider $linkTypeProvider,
        Builder $builder,
        Data\LinkedProductEntityBuilder $productEntityBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Data\LinkedProductEntity\CollectionProvider $entityCollectionProvider,
        Data\LinkAttributeEntityBuilder $linkAttributeBuilder,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        Logger $logger
    ) {
        $this->linkTypeProvider = $linkTypeProvider;
        $this->builder = $builder;
        $this->productFactory = $productFactory;
        $this->productEntityBuilder = $productEntityBuilder;
        $this->entityCollectionProvider = $entityCollectionProvider;
        $this->linkFactory = $linkFactory;
        $this->linkAttributeBuilder = $linkAttributeBuilder;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductLinkTypes()
    {
        $output = [];
        foreach ($this->linkTypeProvider->getLinkTypes() as $type => $typeCode) {
            $data = [LinkTypeEntity::TYPE => $type, LinkTypeEntity::CODE => $typeCode];
            $output[] = $this->builder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts($productId, $type)
    {
        $output = [];
        $product = $this->productFactory->create();
        $product->load($productId);
        try {
            $collection = $this->entityCollectionProvider->getCollection($product, $type);
        } catch (\InvalidArgumentException $exception) {
            $this->logger->logException($exception);
            throw new InputException('Link type is not registered');
        }

        foreach ($collection as $item) {
            /** @var \Magento\Catalog\Model\Product $item */
            $data = [
                LinkedProductEntity::ID => $item->getId(),
                LinkedProductEntity::TYPE => $item->getTypeId(),
                LinkedProductEntity::ATTRIBUTE_SET_ID => $item->getAttributeSetId(),
                LinkedProductEntity::SKU => $item->getSku(),
                LinkedProductEntity::POSITION => $item->getPosition()
            ];
            $output[] = $this->productEntityBuilder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttributes($type)
    {
        $output = [];
        /** @var \Magento\Catalog\Model\Product\Link $link */
        $link = $this->linkFactory->create(['data' => ['link_type_id' => $type]]);
        $attributes = $link->getAttributes();
        foreach ($attributes as $item) {
            $data = [
                Data\LinkAttributeEntity::CODE => $item['code'],
                Data\LinkAttributeEntity::TYPE => $item['type'],
            ];
            $output[] = $this->linkAttributeBuilder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }
}
