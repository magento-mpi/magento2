<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Catalog\Service\V1\Product\Link\Data\LinkTypeEntity;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;
use \Magento\Framework\Logger;

/**
 * Class ReadService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @var Data\LinkTypeEntityBuilder
     */
    protected $builder;

    /**
     * @var ProductLoader
     */
    protected $productLoader;

    /**
     * @var LinkTypeResolver
     */
    protected $linkTypeResolver;

    /**
     * @var Data\ProductLinkEntityBuilder
     */
    protected $productEntityBuilder;

    /**
     * @var Data\ProductLinkEntity\CollectionProvider
     */
    protected $entityCollectionProvider;

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
     * @param Data\LinkTypeEntityBuilder $builder
     * @param Data\ProductLinkEntityBuilder $productEntityBuilder
     * @param ProductLoader $productLoader
     * @param ProductLinkEntity\CollectionProvider $entityCollectionProvider
     * @param Data\LinkAttributeEntityBuilder $linkAttributeBuilder
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param LinkTypeResolver $linkTypeResolver
     */
    public function __construct(
        LinkTypeProvider $linkTypeProvider,
        Data\LinkTypeEntityBuilder $builder,
        Data\ProductLinkEntityBuilder $productEntityBuilder,
        ProductLoader $productLoader,
        Data\ProductLinkEntity\CollectionProvider $entityCollectionProvider,
        Data\LinkAttributeEntityBuilder $linkAttributeBuilder,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        LinkTypeResolver $linkTypeResolver
    ) {
        $this->linkTypeProvider = $linkTypeProvider;
        $this->builder = $builder;
        $this->productLoader = $productLoader;
        $this->productEntityBuilder = $productEntityBuilder;
        $this->entityCollectionProvider = $entityCollectionProvider;
        $this->linkFactory = $linkFactory;
        $this->linkAttributeBuilder = $linkAttributeBuilder;
        $this->linkTypeResolver = $linkTypeResolver;
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
    public function getLinkedProducts($productSku, $type)
    {
        $output = [];
        $product = $this->productLoader->load($productSku);
        $typeId = $this->linkTypeResolver->getTypeIdByCode($type);
        $collection = $this->entityCollectionProvider->getCollection($product, $typeId);
        foreach ($collection as $item) {
            $output[] = $this->productEntityBuilder->populateWithArray($item)->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttributes($type)
    {
        $output = [];
        $typeId = $this->linkTypeResolver->getTypeIdByCode($type);

        /** @var \Magento\Catalog\Model\Product\Link $link */
        $link = $this->linkFactory->create(['data' => ['link_type_id' => $typeId]]);
        $attributes = $link->getAttributes();
        foreach ($attributes as $item) {
            $data = [
                Data\LinkAttributeEntity::CODE => $item['code'],
                Data\LinkAttributeEntity::TYPE => $item['type'],
            ];
            $output[] = $this->linkAttributeBuilder->populateWithArray($data)->create();
        }
        return $output;
    }
}
