<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1;

use Magento\UrlRedirect\Model\Data\Filter;
use Magento\UrlRedirect\Model\Data\FilterFactory;
use Magento\UrlRedirect\Model\Data\UrlRewrite;
use Magento\UrlRedirect\Model\StorageInterface;

/**
 * Url Manager
 */
class UrlManager implements UrlMatcherInterface, UrlPersisterInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @param StorageInterface $storage
     * @param FilterFactory $filterFactory
     */
    public function __construct(StorageInterface $storage, FilterFactory $filterFactory)
    {
        $this->storage = $storage;
        $this->filterFactory = $filterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $urls)
    {
        $this->storage->deleteByFilter($this->createFilter($urls));

        $this->storage->add($urls);
    }

    /**
     * {@inheritdoc}
     */
    public function match($requestPath, $storeId)
    {
        $filter = $this->filterFactory->create();
        $filter->setRequestPath($requestPath)->setStoreId($storeId);

        return $this->findByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEntity($entityId, $entityType, $storeId = 0)
    {
        $filter = $this->filterFactory->create();
        $filter->setEntityId($entityId)->setEntityType($entityType)->setStoreId($storeId);

        return $this->findByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilter(Filter $filter)
    {
        return $this->storage->findByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByFilter(Filter $filter)
    {
        return $this->storage->findAllByFilter($filter);
    }

    /**
     * Get filter for url rows deletion due to provided urls
     *
     * @param UrlRewrite[] $urls
     * @return Filter
     */
    protected function createFilter($urls)
    {
        $filterData = [];
        $uniqueKeys = [UrlRewrite::ENTITY_ID, UrlRewrite::ENTITY_TYPE, UrlRewrite::STORE_ID];
        foreach ($urls as $url) {
            foreach ($uniqueKeys as $key) {
                $fieldValue = $url->getByKey($key);

                if (!isset($filterData[$key]) || !in_array($fieldValue, $filterData[$key])) {
                    $filterData[$key][] = $fieldValue;
                }
            }
        }
        return $this->filterFactory->create(['filterData' => $filterData]);
    }
}
