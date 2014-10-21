<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Catalog\Service\V1\Product\Link\Data\LinkType;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;
use \Magento\Framework\Logger;
use Magento\Catalog\Service\V1\Product\ProductLoader;

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
     * @var Data\LinkTypeBuilder
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
     * @var Data\ProductLinkBuilder
     */
    protected $productEntityBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductLink\CollectionProvider
     */
    protected $entityCollectionProvider;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $linkFactory;

    /**
     * @var Data\LinkAttributeBuilder
     */
    protected $linkAttributeBuilder;

    /**
     * @param LinkTypeProvider $linkTypeProvider
     * @param Data\LinkTypeBuilder $builder
     * @param Data\ProductLinkBuilder $productEntityBuilder
     * @param ProductLoader $productLoader
     * @param \Magento\Catalog\Model\ProductLink\CollectionProvider $entityCollectionProvider
     * @param Data\LinkAttributeBuilder $linkAttributeBuilder
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param LinkTypeResolver $linkTypeResolver
     */
    public function __construct(
        LinkTypeProvider $linkTypeProvider,
        Data\LinkTypeBuilder $builder,
        Data\ProductLinkBuilder $productEntityBuilder,
        ProductLoader $productLoader,
        \Magento\Catalog\Model\ProductLink\CollectionProvider $entityCollectionProvider,
        Data\LinkAttributeBuilder $linkAttributeBuilder,
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
            $data = [LinkType::TYPE => $type, LinkType::CODE => $typeCode];
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
        $collection = $this->entityCollectionProvider->getCollection($product, $type);
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
                Data\LinkAttribute::CODE => $item['code'],
                Data\LinkAttribute::TYPE => $item['type'],
            ];
            $output[] = $this->linkAttributeBuilder->populateWithArray($data)->create();
        }
        return $output;
    }
}
