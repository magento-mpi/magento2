<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\DataProvider;

use Magento\Ui\DataProvider\Config\Data as Config;
use Magento\Framework\ObjectManager;
use Magento\Ui\DataProvider\MetadataFactory;

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
     * @param Config $config
     * @param ObjectManager $objectManager
     * @param MetadataFactory $metadata
     */
    public function __construct(Config $config, ObjectManager $objectManager, MetadataFactory $metadataFactory)
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * returns datasource metadata
     *
     * @param $datasource
     * @return array|mixed|null
     */
    public function getMetadata($datasource)
    {
        return $this->metadataFactory->create([
            'config' => $this->config->getDataSource($datasource)
        ]);

    }

    /**
     * Returns data by specified datasource name
     *
     * @param $datasource
     * @param null $filters
     * @return array
     */
    public function getData($datasource, $filters = null)
    {
        $config = $this->config->getDataSource($datasource);
        /** @var \Magento\Framework\Data\Collection\Db $collection */
        $collection = $this->objectManager->create($config['dataset']);

        foreach ($config['fields'] as $field) {
            if ($field['datatype'] == 'eav') {
                $collection->addAttributeToSelect($field['name']);
            }
        }

        if ($filters) {
            foreach ($filters as $field => $expression) {
                $collection->addFieldToFilter($field, $expression);
            }
        }
        $fields = $config['fields'];
        $rows = [];
        foreach ($collection as $item) {
            $row = [];
            foreach ($fields as $field) {
                if ($field['datatype'] == 'lookup') {
                    $lookup = $this->getData($field['reference']['target'], [$field['reference']['target_field']=> $row[$field['reference']['referenced_field']]]);
                    $row[$field['name']] = $lookup[0][$field['reference']['needed_field']];;
                } else {
                    $row[$field['name']] = $item->getData($field['name']);
                }
            }
            if (!empty($config['children'])) {
                foreach ($config['children'] as $name => $reference) {
                    $filter = [];
                    foreach ($reference as $metadata) {
                        $filter[$metadata['referenced_field']] = $row[$metadata['target_field']];
                    }
                    $row[$name] = $this->getData($name, $filter);
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }
}
