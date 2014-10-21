<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

use Magento\Catalog\Api\Data;
use \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Catalog\Api\Data\ProductLinkInterface as ProductLinkInterface;

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
     * @var \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder
     */
    protected $productLinkBuilder;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $productResource;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param CollectionProvider $collectionProvider
     * @param Data\ProductLinkInterfaceBuilder $productLinkBuilder
     * @param LinksInitializer $linkInitializer
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CollectionProvider $collectionProvider,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder $productLinkBuilder,
        LinksInitializer $linkInitializer,
        \Magento\Catalog\Model\Resource\Product $productResource
    ) {
        $this->productRepository = $productRepository;
        $this->entityCollectionProvider = $collectionProvider;
        $this->productLinkBuilder = $productLinkBuilder;
        $this->productResource = $productResource;
        $this->linkInitializer = $linkInitializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedItemsByType($productSku, $linkType)
    {
        $output = [];
        $product = $this->productRepository->get($productSku);
        $collection = $this->entityCollectionProvider->getCollection($product, $linkType);
        foreach ($collection as $item) {
            $data = [
                ProductLinkInterface::PRODUCT_SKU => $product->getSku(),
                ProductLinkInterface::LINK_TYPE => $linkType,
                ProductLinkInterface::LINKED_PRODUCT_SKU => $item['sku'],
                ProductLinkInterface::LINKED_PRODUCT_TYPE => $item['type'],
                ProductLinkInterface::POSITION => $item['position']
            ];
            $output[] = $this->productLinkBuilder->populateWithArray($data)->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductLinks($productSku, $linkType, array $items)
    {
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface[] $linkedProducts */
        $linkedSkuList = [];
        foreach ($linkedProducts as $link) {
            $linkedSkuList[] = $link->getLinkedProductSku();
            $linkedSkuList[] = $link->getProductSku();
        }
        $linkedSkuList = array_unique($linkedSkuList);
        $linkedProductIds = $this->productResource->getProductsIdsBySkus($linkedSkuList);

        $linksData = [];
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct*/
        foreach ($linkedProducts as $linkedProduct) {
            if (!isset($linkedProductIds[$linkedProduct->getLinkedProductSku()])) {
                throw new NoSuchEntityException(
                    sprintf("Product with SKU \"%s\" does not exist", $linkedProduct->getLinkedProductSku())
                );
            }

            if (!isset($linkedProductIds[$linkedProduct->getProductSku()])) {
                throw new NoSuchEntityException(
                    sprintf("Product with SKU \"%s\" does not exist", $linkedProduct->getProductSku())
                );
            }

            $linksData[$linkedProduct->getProductSku()][$linkedProduct->getLinkType()][] = $linkedProduct;
        }

        foreach ($linksData as $productSku => $links) {
            try {
                $product = $this->productRepository->get($productSku);
                $this->linkInitializer->initializeLinks($product, $links);
                $product->save();
            } catch (\Exception $exception) {
                throw new CouldNotSaveException('Invalid data provided for linked products');
            }
        }

        return true;
    }
}
