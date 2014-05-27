<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;

class WriteService implements WriteServiceInterface
{
    /**
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * @var Data\ProductLinkEntity\CollectionProvider
     */
    protected $entityCollectionProvider;

    /**
     * @var ProductLoader
     */
    protected $productLoader;

    /**
     * @var LinkTypeResolver
     */
    protected $linkTypeResolver;

    /**
     * @param LinksInitializer $linkInitializer
     * @param ProductLinkEntity\CollectionProvider $entityCollectionProvider
     * @param ProductLoader $productLoader
     * @param LinkTypeResolver $linkTypeResolver
     */
    public function __construct(
        LinksInitializer $linkInitializer,
        ProductLinkEntity\CollectionProvider $entityCollectionProvider,
        ProductLoader $productLoader,
        LinkTypeResolver $linkTypeResolver
    ) {
        $this->linkInitializer = $linkInitializer;
        $this->entityCollectionProvider = $entityCollectionProvider;
        $this->linkTypeResolver = $linkTypeResolver;
        $this->productLoader = $productLoader;
    }

    /**
     * Save product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $links
     * @throws CouldNotSaveException
     * @return void
     */
    protected function saveLinks($product, array $links)
    {
        $this->linkInitializer->initializeLinks($product, $links);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException('Invalid data provided for linked products');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assign($productSku, array $assignedProducts, $type)
    {
        $product = $this->productLoader->load($productSku);

        $links = [];
        /** @var Data\ProductLinkEntity[] $assignedProducts*/
        foreach ($assignedProducts as $linkedProduct) {
            $data = $linkedProduct->__toArray();
            $links[$data[Data\ProductLinkEntity::ID]] = $data;
        }
        $this->saveLinks($product, [$type => $links]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $linkedProductSku, $type)
    {
        $product = $this->productLoader->load($productSku);
        $linkedProduct = $this->productLoader->load($linkedProductSku);
        $typeId = $this->linkTypeResolver->getTypeIdByCode($type);
        $collection = $this->entityCollectionProvider->getCollection($product, $typeId);

        $links = [];
        foreach ($collection as $item) {
            /** @var \Magento\Catalog\Model\Product $item */
            $links[$item->getId()] = [
                ProductLinkEntity::ID => $item->getId(),
                ProductLinkEntity::TYPE => $item->getTypeId(),
                ProductLinkEntity::ATTRIBUTE_SET_ID => $item->getAttributeSetId(),
                ProductLinkEntity::SKU => $item->getSku(),
                ProductLinkEntity::POSITION => $item->getPosition()
            ];
        }

        if (!isset($links[$linkedProduct->getId()])) {
            throw new InputException(
                sprintf('Product with SKU %s is not linked to product with SKU %s', [$linkedProductSku, $productSku])
            );
        }

        //Remove product from the linked product list
        unset($links[$linkedProduct->getId()]);

        $this->saveLinks($product, [$type => $links]);

        return true;
    }
}
