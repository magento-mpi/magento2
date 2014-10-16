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
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\ProductLinkInterface $entity, array $arguments = [])
    {
        $linkedProduct = $this->productRepository->get($entity->getLinkedProductSku());
        $product = $this->productRepository->get($entity->getProductSku());
        $links = $this->entityCollectionProvider->getCollection($product, $entity->getLinkType());

        $data = $entity->__toArray();
        $data['product_id'] = $linkedProduct->getId();
        $links[$linkedProduct->getId()] = $data;
        $this->linkInitializer->initializeLinks($product, [$entity->getLinkType() => $links]);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException('Invalid data provided for linked products');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\ProductLinkInterface $entity, array $arguments = [])
    {
        $linkedProduct = $this->productRepository->get($entity->getLinkedProductSku());
        $product = $this->productRepository->get($entity->getProductSku());
        $links = $this->entityCollectionProvider->getCollection($product, $entity->getLinkType());

        if (!isset($links[$linkedProduct->getId()])) {
            throw new NoSuchEntityException(
                sprintf(
                    'Product with SKU %s is not linked to product with SKU %s',
                    $entity->getLinkedProductSku(),
                    $entity->getProductSku()
                )
            );
        }
        //Remove product from the linked product list
        unset($links[$linkedProduct->getId()]);
        $data = $entity->__toArray();
        $data['product_id'] = $linkedProduct->getId();
        $links[$linkedProduct->getId()] = $data;
        $this->linkInitializer->initializeLinks($product, [$entity->getLinkType() => $links]);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException('Invalid data provided for linked products');
        }
        return true;
    }
}
