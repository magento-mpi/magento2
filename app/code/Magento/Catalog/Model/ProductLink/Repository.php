<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

use \Magento\Catalog\Api\Data;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use \Magento\Catalog\Model\Resource\Product as ProductResource;

class Repository implements \Magento\Catalog\Api\ProductLinkRepositoryInterface
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
     * @var \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder
     */
    protected $productLinkBuilder;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Link
     */
    protected $linkResource;

    /**
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param CollectionProvider $collectionProvider
     * @param Data\ProductLinkInterfaceBuilder $productLinkBuilder
     * @param \Magento\Catalog\Model\Resource\Product\Link $linkResource
     * @param LinksInitializer $linkInitializer
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CollectionProvider $collectionProvider,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder $productLinkBuilder,
        \Magento\Catalog\Model\Resource\Product\Link $linkResource,
        ProductResource $productResource,
        LinksInitializer $linkInitializer
    ) {
        $this->productRepository = $productRepository;
        $this->entityCollectionProvider = $collectionProvider;
        $this->productLinkBuilder = $productLinkBuilder;
        $this->linkResource = $linkResource;
        $this->linkInitializer = $linkInitializer;
        $this->productResource = $productResource;
    }

    /**
     * Provide the list of links for a specific product
     *
     * @param string $productSku
     * @param string $type
     * @return \Magento\Catalog\Api\Data\ProductLinkInterface[]
     */
    public function getList($productSku, $type)
    {
        $output = [];
        $product = $this->productRepository->get($productSku);
        $collection = $this->entityCollectionProvider->getCollection($product, $type);
        foreach ($collection as $item) {
            $output[] = $this->productLinkBuilder->populateWithArray($item)->create();
        }
        return $output;
    }

    /**
     * Save product link
     *
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct
     * @return bool
     */
    public function save(\Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct)
    {
        $this->linkResource->save($linkedProduct);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function assign(array $linkedProducts)
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

    /**
     * Delete product link
     *
     * @param Data\ProductLinkInterface $linkedProduct
     * @return bool
     */
    public function delete(\Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct)
    {
        $this->linkResource->delete($linkedProduct);
        return true;
    }

    /**
     * Delete product link by identifier
     *
     * @param string $productSku
     * @param string $linkType
     * @param string $linkedProductSku
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @return bool
     */
    public function deleteByIdentifier($productSku, $linkType, $linkedProductSku)
    {
        $linkedProduct = $this->productRepository->get($linkedProductSku);
        $product = $this->productRepository->get($productSku);
        $links = $this->entityCollectionProvider->getCollection($product, $linkType);

        if (!isset($links[$linkedProduct->getId()])) {
            throw new NoSuchEntityException(
                sprintf('Product with SKU %s is not linked to product with SKU %s', $linkedProductSku, $productSku)
            );
        }

        //Remove product from the linked product list
        unset($links[$linkedProduct->getId()]);

        $this->linkInitializer->initializeLinks($product, $links);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException('Invalid data provided for linked products');
        }
        return true;
    }
}
