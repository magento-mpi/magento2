<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

use Magento\Catalog\Api\Data;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Catalog\Api\Data\ProductLinkInterface as ProductLinkInterface;

class Management implements \Magento\Catalog\Api\ProductLinkManagementInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var CollectionProvider
     */
    protected $entityCollectionProvider;

    /**
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkDataBuilder
     */
    protected $productLinkBuilder;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Framework\Service\Data\AttributeValueBuilder
     */
    protected $valueBuilder;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param CollectionProvider $collectionProvider
     * @param Data\ProductLinkDataBuilder $productLinkBuilder
     * @param LinksInitializer $linkInitializer
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Framework\Api\AttributeValueBuilder $valueBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CollectionProvider $collectionProvider,
        \Magento\Catalog\Api\Data\ProductLinkDataBuilder $productLinkBuilder,
        LinksInitializer $linkInitializer,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Framework\Api\AttributeValueBuilder $valueBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->entityCollectionProvider = $collectionProvider;
        $this->productLinkBuilder = $productLinkBuilder;
        $this->productResource = $productResource;
        $this->linkInitializer = $linkInitializer;
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedItemsByType($productSku, $type)
    {
        $output = [];
        $product = $this->productRepository->get($productSku);
        $collection = $this->entityCollectionProvider->getCollection($product, $type);
        foreach ($collection as $item) {
            $data = [
                ProductLinkInterface::PRODUCT_SKU => $product->getSku(),
                ProductLinkInterface::LINK_TYPE => $type,
                ProductLinkInterface::LINKED_PRODUCT_SKU => $item['sku'],
                ProductLinkInterface::LINKED_PRODUCT_TYPE => $item['type'],
                ProductLinkInterface::POSITION => $item['position'],
            ];
            $this->productLinkBuilder->populateWithArray($data);
            if (isset($item['custom_attributes'])) {
                foreach ($item['custom_attributes'] as $option) {
                    $this->productLinkBuilder->setCustomAttribute(
                        $this->valueBuilder->populateWithArray($option)->create()
                    );
                }
            }
            $output[] = $this->productLinkBuilder->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductLinks($productSku, $type, array $items)
    {
        $product = $this->productRepository->get($productSku);
        $assignedSkuList = [];
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $link */
        foreach ($items as $link) {
            $assignedSkuList[] = $link->getLinkedProductSku();
        }
        $linkedProductIds = $this->productResource->getProductsIdsBySkus($assignedSkuList);

        $links = [];
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface[] $items*/
        foreach ($items as $link) {
            $data = $link->__toArray();
            $linkedSku = $link->getLinkedProductSku();
            if (!isset($linkedProductIds[$linkedSku])) {
                throw new NoSuchEntityException(
                    sprintf("Product with SKU \"%s\" does not exist", $linkedSku)
                );
            }
            $data['product_id'] = $linkedProductIds[$linkedSku];
            $links[$linkedProductIds[$linkedSku]] = $data;
        }
        $this->linkInitializer->initializeLinks($product, [$type => $links]);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException('Invalid data provided for linked products');
        }
        return true;
    }
}
