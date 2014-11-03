<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\DataProvider;

use Magento\Framework\ObjectManager;
use Magento\Ui\DataProvider\MetadataFactory;
use Magento\Ui\DataProvider\Config\Data as Config;

/**
 * Class Manager
 */
class Manager
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param Config $config
     * @param ObjectManager $objectManager
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(Config $config, ObjectManager $objectManager, MetadataFactory $metadataFactory)
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Returns Data Source metadata
     *
     * @param $dataSource
     * @return \Magento\Ui\DataProvider\Metadata
     */
    public function getMetadata($dataSource)
    {
        return $this->metadataFactory->create([
            'config' => $this->config->getDataSource($dataSource)
        ]);
    }

    public function getCollectionData($dataSource, array $filters = [])
    {
        $collectionHash = md5($dataSource . serialize($filters));
        if (!isset($this->cache[$collectionHash])) {
            $config = $this->config->getDataSource($dataSource);
            /** @var \Magento\Framework\Data\Collection\Db $collection */
            $collection = $this->objectManager->create($config['dataset']);

            foreach ($config['fields'] as $field) {
                if (isset($field['source']) && $field['source'] == 'eav') {
                    $collection->addAttributeToSelect($field['name']);
                }
            }

            if ($filters) {
                foreach ($filters as $field => $expression) {
                    $collection->addFieldToFilter($field, $expression);
                }
            }
            $this->cache[$collectionHash] = $collection->getItems();
        }
        return $this->cache[$collectionHash];
    }

    /**
     * Returns data by specified Data Source name
     *
     * @param string $dataSource
     * @param array $filters
     * @return array
     */
    public function getData($dataSource, array $filters = [])
    {
        $config = $this->config->getDataSource($dataSource);

        $items = $this->getCollectionData($dataSource, $filters);

        $fields = $config['fields'];
        $rows = [];
        foreach ($items as $item) {
            $row = [];
            foreach ($fields as $field) {
                if (isset($field['source']) && $field['source'] == 'lookup') {
                    $lookupCollection = $this->getCollectionData($field['reference']['target'],
                        [$field['reference']['targetField']=> $item->getData($field['reference']['referencedField'])]
                    );
                    $lookup = reset($lookupCollection);
                    $row[$field['name']] = $lookup[$field['reference']['neededField']];
                } elseif (isset($field['source']) && $field['source'] == 'reference') {
                    $lookupCollection = $this->getCollectionData($field['reference']['target'],
                        [$field['reference']['targetField']=> $item->getData($field['reference']['referencedField'])]
                    );
                    $lookup = reset($lookupCollection);
                    $isReferenced = isset($lookup[$field['reference']['neededField']])
                        && $lookup[$field['reference']['neededField']] == $item->getId();
                    $row[$field['name']] = $isReferenced;
                } elseif (isset($field['source']) && $field['source'] == 'option') {
                    $row[$field['name']] = $item->getData($field['reference']['referencedField']);
                } else {
                    $row[$field['name']] = $item->getData($field['name']);
                }
            }
            if (!empty($config['children'])) {
                foreach ($config['children'] as $name => $reference) {
                    $filter = [];
                    foreach ($reference as $metadata) {
                        $filter[$metadata['referencedField']] = $row[$metadata['targetField']];
                    }
                    $row[$name] = $this->getData($name, $filter);
                }
            }
            $rows[$item->getId()] = $row;
        }
        return $rows;
    }
}
