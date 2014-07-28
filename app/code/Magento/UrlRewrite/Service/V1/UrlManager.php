<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\Data\Filter;
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
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $urls)
    {
        if (!$urls) {
            throw new \InvalidArgumentException('Passed rewrites is empty.');
        }
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
}
