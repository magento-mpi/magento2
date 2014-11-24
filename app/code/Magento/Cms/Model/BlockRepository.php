<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BlockRepository
 * @api
 */
class BlockRepository implements BlockRepositoryInterface
{
    /**
     * @var \Magento\Cms\Model\Resource\Block
     */
    protected $resource;

    /**
     * @var \Magento\Cms\Api\Data\BlockInterfaceFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Cms\Api\Data\BlockCollectionInterfaceFactory
     */
    protected $blockCollectionFactory;

    /**
     * @var \Magento\Framework\DB\QueryBuilderFactory
     */
    protected $queryBuilderFactory;

    /**
     * @var \Magento\Framework\DB\MapperFactory
     */
    protected $mapperFactory;

    /**
     * @param Resource\Block $resource
     * @param \Magento\Cms\Api\Data\BlockInterfaceFactory $blockFactory
     * @param \Magento\Cms\Api\Data\BlockCollectionInterfaceFactory $blockCollectionFactory
     * @param \Magento\Framework\DB\QueryBuilderFactory $queryBuilderFactory
     * @param \Magento\Framework\DB\MapperFactory $mapperFactory
     */
    public function __construct(
        \Magento\Cms\Model\Resource\Block $resource,
        \Magento\Cms\Api\Data\BlockInterfaceFactory $blockFactory,
        \Magento\Cms\Api\Data\BlockCollectionInterfaceFactory $blockCollectionFactory,
        \Magento\Framework\DB\QueryBuilderFactory $queryBuilderFactory,
        \Magento\Framework\DB\MapperFactory $mapperFactory
    ) {
        $this->resource = $resource;
        $this->blockFactory = $blockFactory;
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * Save Block data
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return \Magento\Cms\Api\Data\BlockInterface
     * @throws CouldNotSaveException
     */
    public function save(\Magento\Cms\Api\Data\BlockInterface $block)
    {
        try {
            $this->resource->save($block);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException($exception->getMessage());
        }
        return $block;
    }

    /**
     * Load Block data by given Block Identity
     *
     * @param string $blockId
     * @return \Magento\Cms\Api\Data\BlockInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($blockId)
    {
        $block = $this->blockFactory->create();
        $this->resource->load($block, $blockId);
        if (!$block->getBlockId()) {
            throw new NoSuchEntityException(sprintf('CMS Block with id "%s" does not exist.', $blockId));
        }
        return $block;
    }

    /**
     * Load Block data collection by given search criteria
     *
     * @param \Magento\Cms\Api\BlockCriteriaInterface $criteria
     * @return \Magento\Cms\Api\Data\BlockCollectionInterface
     */
    public function getList(\Magento\Cms\Api\BlockCriteriaInterface $criteria)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->setCriteria($criteria);
        $queryBuilder->setResource($this->resource);
        $query = $queryBuilder->create();
        $collection = $this->blockCollectionFactory->create(['query' => $query]);
        return $collection;
    }

    /**
     * Delete Block
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magento\Cms\Api\Data\BlockInterface $block)
    {
        try {
            $this->resource->delete($block);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException($exception->getMessage());
        }
        return true;
    }

    /**
     * Delete Block by given Block Identity
     *
     * @param string $blockId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($blockId)
    {
        return $this->delete($this->get($blockId));
    }
}
