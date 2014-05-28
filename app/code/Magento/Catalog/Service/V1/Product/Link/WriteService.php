<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Catalog\Model\Resource\Product as ProductResource;

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
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $productResource;

    /**
     * @var Data\ProductLinkEntity\DataMapperInterface
     */
    protected $dataMapper;

    /**
     * @param LinksInitializer $linkInitializer
     * @param ProductLinkEntity\CollectionProvider $entityCollectionProvider
     * @param ProductLoader $productLoader
     * @param LinkTypeResolver $linkTypeResolver
     * @param ProductResource $productResource
     * @param ProductLinkEntity\DataMapperInterface $dataMapper
     */
    public function __construct(
        LinksInitializer $linkInitializer,
        ProductLinkEntity\CollectionProvider $entityCollectionProvider,
        ProductLoader $productLoader,
        LinkTypeResolver $linkTypeResolver,
        ProductResource $productResource,
        Data\ProductLinkEntity\DataMapperInterface $dataMapper
    ) {
        $this->linkInitializer = $linkInitializer;
        $this->entityCollectionProvider = $entityCollectionProvider;
        $this->linkTypeResolver = $linkTypeResolver;
        $this->productLoader = $productLoader;
        $this->productResource = $productResource;
        $this->dataMapper = $dataMapper;
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
        foreach ($links as $type => $linksData) {
            $links[$type] = $this->dataMapper->map($linksData);
        }

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
        $assignedSkuList = array_map(
            function ($item) {
                return $item->getSku();
            },
            $assignedProducts
        );
        $linkedProductIds = $this->productResource->getProductsIdsBySkus($assignedSkuList);

        $links = [];
        /** @var Data\ProductLinkEntity[] $assignedProducts*/
        foreach ($assignedProducts as $linkedProduct) {
            $data = $linkedProduct->__toArray();
            if (!isset($linkedProductIds[$linkedProduct->getSku()])) {
                throw new NoSuchEntityException(
                    sprintf("Product with SKU \"%s\" does not exist", $linkedProduct->getSku())
                );
            }
            $data['product_id'] = $linkedProductIds[$linkedProduct->getSku()];
            $links[$linkedProductIds[$linkedProduct->getSku()]] = $data;
        }
        $this->saveLinks($product, [$type => $links]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update($productSku, Data\ProductLinkEntity $linkedProduct, $type)
    {
        $product = $this->productLoader->load($productSku);
        $linkedProductEntity = $this->productLoader->load($linkedProduct->getSku());
        $links = $this->retrieveLinkedProducts($product, $type);
        if (!isset($links[$linkedProductEntity->getId()])) {
            throw new NoSuchEntityException(
                sprintf(
                    "Product with SKU \"%s\" is not linked to product with SKU %s",
                    [$linkedProduct->getSku(), $productSku]
                )
            );
        }


        /** @var \Magento\Catalog\Model\Product $item */
        $links[$linkedProductEntity->getId()] = [
            'product_id' => $linkedProductEntity->getId(),
            ProductLinkEntity::TYPE => $linkedProduct->getType(),
            ProductLinkEntity::ATTRIBUTE_SET_ID => $linkedProduct->getAttributeSetId(),
            ProductLinkEntity::SKU => $linkedProduct->getSku(),
            ProductLinkEntity::POSITION => $linkedProduct->getPosition()
        ];

        $this->saveLinks($product, [$type => $links]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $linkedProductSku, $type)
    {
        $linkedProduct = $this->productLoader->load($linkedProductSku);
        $product = $this->productLoader->load($productSku);
        $links = $this->retrieveLinkedProducts($product, $type);

        if (!isset($links[$linkedProduct->getId()])) {
            throw new NoSuchEntityException(
                sprintf('Product with SKU %s is not linked to product with SKU %s', [$linkedProductSku, $productSku])
            );
        }

        //Remove product from the linked product list
        unset($links[$linkedProduct->getId()]);

        $this->saveLinks($product, [$type => $links]);

        return true;
    }

    /**
     * Retrieve product links information
     *
     * @param string $type
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function retrieveLinkedProducts(\Magento\Catalog\Model\Product $product, $type)
    {
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
        return $links;
    }
}
