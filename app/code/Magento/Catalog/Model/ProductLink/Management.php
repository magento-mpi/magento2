<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

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
     * @var \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder
     */
    protected $productLinkBuilder;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param CollectionProvider $collectionProvider
     * @param \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder $productLinkBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CollectionProvider $collectionProvider,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder $productLinkBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->entityCollectionProvider = $collectionProvider;
        $this->productLinkBuilder = $productLinkBuilder;
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
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param string $linkType
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $items
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::assign - previous implementation
     */
    public function assign($productSku, $linkType, array $items)
    {

    }

    /**
     * Update product link
     *
     * @param string $productSku
     * @param string $linkType
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::update - prevuois implementation
     */
    public function update($productSku, $linkType, \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct)
    {

    }

    /**
     * Remove the product link from a specific product
     *
     * @param string $productSku
     * @param string $linkType
     * @param string $linkedProductSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::remove - prevuois implementation
     */
    public function remove($productSku, $linkType, $linkedProductSku)
    {

    }
}
