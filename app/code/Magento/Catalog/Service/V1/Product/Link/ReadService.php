<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Catalog\Service\V1\Product\Link\Data\LinkTypeEntityBuilder as Builder;
use \Magento\Catalog\Service\V1\Product\Link\Data\LinkTypeEntity;
use \Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Logger;

class ReadService implements ReadServiceInterface
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
    public function getLinkedProducts($productSku, $type)
    {
        $output = [];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $productId = $product->getIdBySku($productSku);

        if (!$productId) {
            throw new InputException('There is no product with provided SKU');
        }
        $product->load($productId);

        try {
            $typeId = $this->getTypeId($type);
            $collection = $this->entityCollectionProvider->getCollection($product, $typeId);
        } catch (\OutOfRangeException $exception) {
            throw new InputException($exception->getMessage());
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
        try {
            $typeId = $this->getTypeId($type);
        } catch (\OutOfRangeException $exception) {
            throw new InputException($exception->getMessage());
        }
        $output = [];
        /** @var \Magento\Catalog\Model\Product\Link $link */
        $link = $this->linkFactory->create(['data' => ['link_type_id' => $typeId]]);
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

    /**
     * Get link type id by code
     *
     * @param string $code
     * @throws \OutOfRangeException
     * @return int
     */
    protected function getTypeId($code)
    {
        $types = $this->linkTypeProvider->getLinkTypes();
        if (isset($types[$code])) {
            return $types[$code];
        }
        throw new \OutOfRangeException('Unknown link type code is provided');
    }
}
