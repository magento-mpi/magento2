<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PageRepository
 * @api
 */
class PageRepository implements PageRepositoryInterface
{
    /**
     * @var \Magento\Cms\Model\Resource\Page
     */
    protected $resource;

    /**
     * @var \Magento\Cms\Api\Data\PageInterfaceFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Cms\Api\Data\PageCollectionInterfaceFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var \Magento\Framework\DB\QueryBuilderFactory
     */
    protected $queryBuilderFactory;

    /**
     * @var \Magento\Framework\DB\MapperFactory
     */
    protected $mapperFactory;

    /**
     * @param Resource\Page $resource
     * @param \Magento\Cms\Api\Data\PageInterfaceFactory $pageFactory
     * @param \Magento\Cms\Api\Data\PageCollectionInterfaceFactory $pageCollectionFactory
     * @param \Magento\Framework\DB\QueryBuilderFactory $queryBuilderFactory
     * @param \Magento\Framework\DB\MapperFactory $mapperFactory
     */
    public function __construct(
        \Magento\Cms\Model\Resource\Page $resource,
        \Magento\Cms\Api\Data\PageInterfaceFactory $pageFactory,
        \Magento\Cms\Api\Data\PageCollectionInterfaceFactory $pageCollectionFactory,
        \Magento\Framework\DB\QueryBuilderFactory $queryBuilderFactory,
        \Magento\Framework\DB\MapperFactory $mapperFactory
    ) {
        $this->resource = $resource;
        $this->pageFactory = $pageFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * Save Page data
     *
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @return \Magento\Cms\Api\Data\PageInterface
     * @throws CouldNotSaveException
     */
    public function save(\Magento\Cms\Api\Data\PageInterface $page)
    {
        try {
            $this->resource->save($page);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException($exception->getMessage());
        }
        return $page;
    }

    /**
     * Load Page data by given Page Identity
     *
     * @param string $pageId
     * @return \Magento\Cms\Api\Data\PageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($pageId)
    {
        $page = $this->pageFactory->create();
        $this->resource->load($page, $pageId);
        if (!$page->getId()) {
            throw new NoSuchEntityException(sprintf('CMS Page with id "%s" does not exist.', $pageId));
        }
        return $page;
    }

    /**
     * Load Page data collection by given search criteria
     *
     * @param \Magento\Cms\Api\PageCriteriaInterface $criteria
     * @return \Magento\Cms\Api\Data\PageCollectionInterface
     */
    public function getList(\Magento\Cms\Api\PageCriteriaInterface $criteria)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->setCriteria($criteria);
        $queryBuilder->setResource($this->resource);
        $query = $queryBuilder->create();
        $collection = $this->pageCollectionFactory->create(['query' => $query]);
        return $collection;
    }

    /**
     * Delete Page
     *
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magento\Cms\Api\Data\PageInterface $page)
    {
        try {
            $this->resource->delete($page);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException($exception->getMessage());
        }
        return true;
    }

    /**
     * Delete Page by given Page Identity
     *
     * @param string $pageId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($pageId)
    {
        return $this->delete($this->get($pageId));
    }
}
