<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Resource\Product\Collection;
use Magento\Framework\Data\Search\SearchCriteriaInterface;
use Magento\Framework\Data\Search\SortOrderInterface;
use Magento\Framework\Data\Search\FilterGroupInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class ProductRepository implements \Magento\Catalog\Api\ProductRepositoryInterface
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product[]
     */
    protected $instances = array();

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Framework\Data\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Data\Search\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $resourceModel;

    /**
     * @var \Magento\Catalog\Model\Resource\ProductFactory
     */
    protected $resourceModelFactory;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Api\Data\ProductSearchResultsBuilder $searchResultsBuilder,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Data\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\Resource\ProductFactory $resourceModelFactory,
        \Magento\Framework\Data\Search\FilterBuilder $filterBuilder
    ) {
        $this->productFactory = $productFactory;
        $this->collectionFactory = $collectionFactory;
        $this->initializationHelper = $initializationHelper;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceModelFactory = $resourceModelFactory;
        $this->attributeRepository = $attributeRepository;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sku, array $arguments = [])
    {
        $editMode = array_key_exists('edit_mode', $arguments) ? $arguments['edit_mode'] : false;
        if (!isset($this->instances[$sku])) {
            $product = $this->productFactory->create();
            $productId = $product->getIdBySku($sku);
            if (!$productId) {
                throw new NoSuchEntityException('Requested product doesn\'t exist');
            }
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            $product->load($productId);
            $this->instances[$sku] = $product;
        }
        return $this->instances[$sku];
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save(\Magento\Catalog\Api\Data\ProductInterface $product, array $arguments = [])
    {
        $this->resourceModel = $this->resourceModelFactory->create();
        try {
            $this->initializationHelper->initialize($product);
            $this->resourceModel->validate($product);
            $this->resourceModel->save($product);
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $exception) {
            throw \Magento\Framework\Exception\InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $product->getData($exception->getAttributeCode()),
                $exception
            );
        }
        if (!$product->getId()) {
            throw new \Magento\Framework\Exception\StateException('Unable to save product');
        }
        $this->instances[$product->getSku()] = $product;
        return $product;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete($productSku, array $arguments = [])
    {
        $this->resourceModel = $this->resourceModelFactory->create();
        $product = $this->get($productSku);
        try {
            $this->resourceModel->delete($product);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException('Unable to remove product ' . $productSku);
        }
        if (array_key_exists($productSku, $this->instances)) {
            unset($this->instances[$productSku]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getList(SearchCriteriaInterface $searchCriteria, array $arguments = [])
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
        $collection = $this->collectionFactory->create();

        // This is needed to make sure all the attributes are properly loaded
        $attributeSearchCriteria = $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('attribute_set_id')
                    ->setValue(ProductAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID)
                    ->create()
            ]
        );

        $attributeSearchResult = $this->attributeRepository->getList($attributeSearchCriteria);
        foreach ($attributeSearchResult->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        /** @var SortOrderInterface $sortOrder*/
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SearchCriteriaInterface::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();
        $products = $collection->getItems();

        $this->searchResultsBuilder->setItems($products);
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        return $this->searchResultsBuilder->create();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroupInterface $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroupInterface $filterGroup, Collection $collection)
    {
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = array('attribute' => $filter->getField(), $condition => $filter->getValue());
        }
        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }
}
