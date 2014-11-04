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
use \Magento\Framework\Service\V1\Data\Search\FilterGroup;
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
     * @var \Magento\Framework\Data\Search\SearchResultsInterfaceBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Framework\Data\Search\SearchCriteriaInterfaceBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Data\Search\FilterInterfaceBuilder
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
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * @param ProductFactory $productFactory
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
     * @param \Magento\Catalog\Api\Data\ProductSearchResultsBuilder $searchResultsBuilder
     * @param Resource\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterfaceBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param Resource\Product $resourceModel
     * @param \Magento\Framework\Data\Search\FilterInterfaceBuilder $filterBuilder
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Service\V1\Data\Product\SearchResultsBuilder $searchResultsBuilder,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\Resource\Product $resourceModel,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface
    ) {
        $this->productFactory = $productFactory;
        $this->collectionFactory = $collectionFactory;
        $this->initializationHelper = $initializationHelper;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceModel = $resourceModel;
        $this->attributeRepository = $attributeRepository;
        $this->filterBuilder = $filterBuilder;
        $this->metadataService = $metadataServiceInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sku, $editMode = false)
    {
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
     */
    public function save(\Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
    {
        $this->initializationHelper->initialize($product);
        $validationResult = $this->resourceModel->validate($product);
        if (true !== $validationResult) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                sprintf('Invalid product data: %s', implode(',', $validationResult))
            );
        }
        try {
            if ($saveOptions) {
                $product->setCanSaveCustomOptions(true);
            }
            $this->resourceModel->save($product);
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $exception) {
            throw \Magento\Framework\Exception\InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $product->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException('Unable to save product');
        }
        if (array_key_exists($product->getSku(), $this->instances)) {
            unset($this->instances[$product->getSku()]);
        }

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $productSku = $product->getSku();
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
     */
    public function deleteById($productSku)
    {
        $product = $this->get($productSku);
        return $this->delete($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
        $collection = $this->collectionFactory->create();

        $extendedSearchCriteria = $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('attribute_set_id')
                    ->setValue(\Magento\Catalog\Api\Data\ProductAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID)
                    ->create()
            ]
        );

        $customAttributes = [];
        foreach ($this->metadataService->getList($extendedSearchCriteria->create())->getItems() as $metadata) {
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
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(\Magento\Framework\Service\V1\Data\Search\FilterGroup $filterGroup,
                                                  Collection $collection)
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
