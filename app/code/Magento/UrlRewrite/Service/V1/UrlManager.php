<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\Data\Filter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\StorageInterface;

/**
 * Url Manager
 */
class UrlManager implements UrlMatcherInterface, UrlPersistInterface
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
    public function replace(array $urls)
    {
        if (!$urls) {
            throw new \InvalidArgumentException('Passed rewrites is empty.');
        }
        $this->storage->deleteByFilter($this->createFilterBasedOnUrls($urls));
        $this->storage->addMultiple($urls);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByFilter(Filter $filter)
    {
        $this->storage->deleteByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function match($requestPath, $storeId)
    {
        /** @var Filter $filter */
        $filter = $this->filterFactory->create();
        $filter->setRequestPath($requestPath)->setStoreId($storeId);

        return $this->findByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEntity($entityId, $entityType, $storeId = 0)
    {
        /** @var Filter $filter */
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
    protected function createFilterBasedOnUrls($urls)
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
