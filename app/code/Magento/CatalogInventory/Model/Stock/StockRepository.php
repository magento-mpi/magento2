<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Stock;

use Magento\CatalogInventory\Api\Data\StockInterface;
use Magento\CatalogInventory\Api\StockRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockCollectionInterfaceFactory;
use Magento\CatalogInventory\Model\Resource\Stock as StockResource;
use Magento\CatalogInventory\Model\StockFactory;
use Magento\Framework\DB\QueryBuilderFactory;
use Magento\Framework\DB\MapperFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class StockRepository
 * @api
 */
class StockRepository implements StockRepositoryInterface
{
    /**
     * @var StockResource
     */
    protected $resource;

    /**
     * @var StockFactory
     */
    protected $stockFactory;

    /**
     * @var StockCollectionInterfaceFactory
     */
    protected $stockCollectionFactory;

    /**
     * @var QueryBuilderFactory
     */
    protected $queryBuilderFactory;

    /**
     * @var MapperFactory
     */
    protected $mapperFactory;

    /**
     * @param StockResource $resource
     * @param StockFactory $stockFactory
     * @param StockCollectionInterfaceFactory $collectionFactory
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param MapperFactory $mapperFactory
     */
    public function __construct(
        StockResource $resource,
        StockFactory $stockFactory,
        StockCollectionInterfaceFactory $collectionFactory,
        QueryBuilderFactory $queryBuilderFactory,
        MapperFactory $mapperFactory
    ) {
        $this->resource = $resource;
        $this->stockFactory = $stockFactory;
        $this->stockCollectionFactory = $collectionFactory;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * @param StockInterface $stock
     * @return StockInterface
     * @throws CouldNotSaveException
     */
    public function save(StockInterface $stock)
    {
        try {
            $this->resource->save($stock);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException($exception->getMessage());
        }
        return $stock;
    }

    /**
     * @param string $stockId
     * @return StockInterface|\Magento\CatalogInventory\Model\Stock
     * @throws NoSuchEntityException
     */
    public function get($stockId)
    {
        $stock = $this->stockFactory->create();
        $this->resource->load($stock, $stockId);
        if (!$stock->getId()) {
            throw new NoSuchEntityException(sprintf('Stock with id "%s" does not exist.', $stockId));
        }
        return $stock;
    }

    /**
     * @param \Magento\CatalogInventory\Api\StockCriteriaInterface $criteria
     * @return \Magento\CatalogInventory\Api\Data\StockCollectionInterface
     */
    public function getList(\Magento\CatalogInventory\Api\StockCriteriaInterface $criteria)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->setCriteria($criteria);
        $queryBuilder->setResource($this->resource);
        $query = $queryBuilder->create();
        $collection = $this->stockCollectionFactory->create(['query' => $query]);
        return $collection;
    }

    /**
     * @param StockInterface $stock
     * @return bool|true
     * @throws CouldNotDeleteException
     */
    public function delete(StockInterface $stock)
    {
        try {
            $this->resource->delete($stock);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException($exception->getMessage());
        }
        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($id)
    {
        try {
            $stock = $this->get($id);
            $this->delete($stock);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException($exception->getMessage());
        }
        return true;
    }
}
