<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Converter;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Catalog\Service\V1\Data\Product as ProductData;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Catalog\Model\Resource\Product\Collection;

/**
 * Class ProductService
 * @package Magento\Catalog\Service\V1
 */
class ProductService implements ProductServiceInterface
{
    /**
     * @var Product\Initialization\Helper
     */
    private $initializationHelper;

    /**
     * @var \Magento\Catalog\Service\V1\Data\ProductMapper
     */
    protected $productMapper;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    private $productTypeManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    private $productCollection;

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
     * @param Data\ProductMapper $productMapper
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollection
     * @param ProductMetadataServiceInterface $metadataService
     * @param \Magento\Catalog\Service\V1\Data\Converter $converter
     * @param Data\SearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Service\V1\Data\ProductMapper $productMapper,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollection,
        ProductMetadataServiceInterface $metadataService,
        \Magento\Catalog\Service\V1\Data\Converter $converter,
        Data\SearchResultsBuilder $searchResultsBuilder
    ) {
        $this->initializationHelper = $initializationHelper;
        $this->productMapper = $productMapper;
        $this->productTypeManager = $productTypeManager;
        $this->productFactory = $productFactory;
        $this->productCollection = $productCollection;
        $this->metadataService = $metadataService;
        $this->converter = $converter;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        try {
            $productModel = $this->productMapper->toModel($product);
            $this->initializationHelper->initialize($productModel);
            $productModel->validate();
            $productModel->save();
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $exception) {
            throw \Magento\Framework\Exception\InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $productModel->getData($exception->getAttributeCode()),
                $exception
            );
        }
        if (!$productModel->getId()) {
            throw new \Magento\Framework\Exception\StateException('Unable to save product');
        }
        return $productModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Product $product)
    {
        $productModel = $this->productFactory->create();
        $productModel->load($id);
        if (!$productModel->getId()) {
            throw NoSuchEntityException::singleField('id', $product->getId());
        }
        try {
            $this->productMapper->toModel($product, $productModel);
            $this->initializationHelper->initialize($productModel);
            $this->productTypeManager->processProduct($productModel);
            $productModel->validate();
            $productModel->save();
        } catch  (\Magento\Eav\Model\Entity\Attribute\Exception $exception) {
            throw \Magento\Framework\Exception\InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $productModel->getData($exception->getAttributeCode()),
                $exception
            );
        }
        return $productModel->getId();
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
    {
        $product = $this->productFactory->create();
        $product->load($id);
        if (!$product->getId()) {
            // product does not exist
            throw NoSuchEntityException::singleField('id', $id);
        }
        return $this->converter->createProductDataFromModel($product);
    }

    /**
     * {@inheritdoc}
     * Example of request:
     * {
     *     "searchCriteria": {
     *         "filterGroups": [
     *             {
     *                 "filters": [
     *                     {"value": "16.000", "conditionType" : "eq", "field" : "price"}
     *                 ]
     *             }
     *         ]
     *     },
     *     "sort_orders" : {"id": "1"},
     *     "page_size" : "30",
     *     "current_page" : "10"
     * }
     *
     * products?searchCriteria[filterGroups][0][filters][0][field]=price&
     * searchCriteria[filterGroups][0][filters][0][value]=16.000&page_size=30&current_page=1&sort_orders[id]=1
     */
    public function search(SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
        $collection = $this->productCollection->create();
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->metadataService->getProductAttributesMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $field = $this->translateField($field);
                $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $this->searchResultsBuilder->setTotalCount($collection->getSize());

        $products = array();
        /** @var \Magento\Catalog\Model\Product $productModel */
        foreach ($collection as $productModel) {
            $products[] = $this->converter->createProductDataFromModel($productModel);
        }

        $this->searchResultsBuilder->setItems($products);
        return $this->searchResultsBuilder->create();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $field = $this->translateField($filter->getField());
            $fields[] = array('attribute' => $field, $condition => $filter->getValue());
        }
        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }

    /**
     * Translates a field name to a DB column name for use in collection queries.
     *
     * @param string $field a field name that should be translated to a DB column name.
     * @return string
     */
    protected function translateField($field)
    {
        switch ($field) {
            case ProductData::ID:
                return 'entity_id';
            default:
                return $field;
        }
    }
}
