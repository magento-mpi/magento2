<?php

namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Converter;

class ProductService implements ProductServiceInterface
{
    /**
     * @var Product\Initialization\Helper
     */
    private $initializationHelper;

    /**
     * @var Product\Builder
     */
    private $productBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    private $productTypeManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var ProductMetadataServiceInterface
     */
    private $metadataService;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var Data\SearchResultsBuilder
     */
    private $searchResultsBuilder;

    /**
     * @param Product\Initialization\Helper $initializationHelper
     * @param Product\Builder $productBuilder
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param ProductMetadataServiceInterface $metadataService
     * @param Data\SearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        Product\Initialization\Helper $initializationHelper,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ProductMetadataServiceInterface $metadataService,
        \Magento\Catalog\Model\Converter $converter,
        Data\SearchResultsBuilder $searchResultsBuilder
    ) {
        $this->initializationHelper = $initializationHelper;
        $this->productBuilder = $productBuilder;
        $this->productTypeManager = $productTypeManager;
        $this->productFactory = $productFactory;
        $this->metadataService = $metadataService;
        $this->converter = $converter;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        $product = $this->productBuilder->build($product);
        $this->initializationHelper->initialize($product);
        $this->productTypeManager->processProduct($product);
        $product->save();
        if (!$product->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Unable to save product'));
        }
        return $product->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $product = $this->productFactory->create();
        $product->load($id);
        if (!$product->getId()) {
            // product does not exist
            throw NoSuchEntityException::singleField('id', $id);
        }
        $product->delete();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {}

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
        $collection = $this->productFactory->create()->getCollection();
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->metadataService->getProductAttributesMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        $products = array();
        /** @var \Magento\Catalog\Model\Product $productModel */
        foreach ($collection as $productModel) {
            $products[] = $this->converter->createProductDataFromModel($productModel);
        }

        $this->searchResultsBuilder->setItems($products);
        return $this->searchResultsBuilder->create();
    }
}
