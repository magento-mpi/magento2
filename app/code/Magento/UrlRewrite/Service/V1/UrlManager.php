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
use Magento\UrlRewrite\Model\Storage\DuplicateEntryException;

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
            return;
        }
        $this->storage->deleteByFilter($this->createFilterBasedOnUrls($urls));
        try {
            $this->storage->addMultiple($urls);
        } catch (DuplicateEntryException $e) {
            throw new DuplicateEntryException(__('URL key for specified store already exists.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByEntityData(array $filterData)
    {
        $filter = $this->filterFactory->create(['data' => $filterData]);
        $this->storage->deleteByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function match($requestPath, $storeId)
    {
        return $this->findByData([
            UrlRewrite::REQUEST_PATH => $requestPath,
            UrlRewrite::STORE_ID => $storeId,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEntity($entityId, $entityType, $storeId = 0)
    {
        return $this->findByData([
            UrlRewrite::ENTITY_ID => $entityId,
            UrlRewrite::ENTITY_TYPE => $entityType,
            UrlRewrite::STORE_ID => $storeId,
        ]);
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
        $data = [];
        $uniqueKeys = [UrlRewrite::REQUEST_PATH, UrlRewrite::STORE_ID];
        foreach ($urls as $url) {
            foreach ($uniqueKeys as $key) {
                $fieldValue = $url->getByKey($key);

                if (!isset($data[$key]) || !in_array($fieldValue, $data[$key])) {
                    $data[$key][] = $fieldValue;
                }
            }
        }
        return $this->filterFactory->create(['data' => $data]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByData(array $data)
    {
        return $this->storage->findByFilter($this->filterFactory->create(['data' => $data]));
    }
}
