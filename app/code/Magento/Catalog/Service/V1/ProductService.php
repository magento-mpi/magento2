<?php

namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Converter;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Catalog\Service\V1\Data\Product as ProductData;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Catalog\Model\Resource\Product\Collection;

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
    public function getAll(SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
        $collection = $this->productFactory->create()->getCollection();
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
