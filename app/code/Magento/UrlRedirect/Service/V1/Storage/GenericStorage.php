<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Storage;

use Magento\UrlRedirect\Service\V1\StorageInterface;
use Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory;
use Magento\UrlRedirect\Service\V1\Storage\Data\Converter;

/**
 * Generic Url Storage
 */
class GenericStorage implements StorageInterface
{
    /**
     * @var \Magento\UrlRedirect\Service\V1\Storage\AdapterInterface
     */
    protected $storageAdapter;

    /**
     * @var \Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory
     */
    protected $filterFactory;

    /**
     * @var \Magento\UrlRedirect\Service\V1\Storage\Data\Converter
     */
    protected $converter;

    /**
     * @param \Magento\UrlRedirect\Service\V1\Storage\AdapterInterface $storageAdapter
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory $filterFactory
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Converter $converter
     */
    public function __construct(AdapterInterface $storageAdapter, FilterFactory $filterFactory, Converter $converter)
    {
        $this->storageAdapter = $storageAdapter;
        $this->filterFactory = $filterFactory;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $urls)
    {
        $deletionFilter = $this->getFilterForDelete($urls);
        $this->storageAdapter->delete($deletionFilter);
        return $this->storageAdapter->add($this->converter->convertObjectsToArray($urls));
    }

    /**
     * Get filter for url rows deletion due to provided urls
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\AbstractData[] $urls
     * @return \Magento\UrlRedirect\Service\V1\Storage\Data\Filter
     */
    protected function getFilterForDelete(array $urls)
    {
        $filterData = [];
        $uniqueKeys = [Data\AbstractData::ENTITY_ID, Data\AbstractData::ENTITY_TYPE, Data\AbstractData::STORE_ID];
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

    /**
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return mixed
     */
    protected function delete(Data\Filter $filter)
    {
        return $this->storageAdapter->delete($filter);
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
    public function matchByEntity($entityId, $entityType, $storeId = 0)
    {
        $filter = $this->filterFactory->create();
        $filter->setEntityId($entityId)->setEntityType($entityType)->setStoreId($storeId);

        return $this->findByFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilter(Data\Filter $filter)
    {
        return $this->converter->convertArrayToObject($this->storageAdapter->find($filter));
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByFilter(Data\Filter $filter)
    {
        $rows = $this->storageAdapter->findAll($filter);
        $storageDataList = [];
        foreach ($rows as $row) {
            $storageDataList[] = $this->converter->convertArrayToObject($row);
        }
        return $storageDataList;
    }
}
