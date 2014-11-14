<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\OfflineShipping\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Tablerate
 */
class Tablerate implements SetupInterface
{
    /**
     * @var \Magento\OfflineShipping\Model\Resource\Carrier\Tablerate
     */
    protected $tablerate;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Directory\Model\Resource\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @param \Magento\OfflineShipping\Model\Resource\Carrier\Tablerate $tablerate,
     * @param \Magento\Tools\SampleData\Helper\Fixture $fixtureHelper
     * @param \Magento\Tools\SampleData\Helper\Csv\ReaderFactory $csvReaderFactory
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\OfflineShipping\Model\Resource\Carrier\Tablerate $tablerate,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Framework\App\Resource $resource,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->tablerate = $tablerate;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->resource = $resource;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing Tablerate' . PHP_EOL;
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $adapter */
        $adapter = $this->resource->getConnection('core_write');
        $fixtureFile = 'OfflineShipping/tablerate.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        $regions = $this->loadDirectoryRegions();
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $data) {
            $regionId = ($data['region'] != '*')
                ? $regions[$data['country']][$data['region']]
                : 0;
            $adapter->insert(
                $adapter->getTableName('shipping_tablerate'),
                [
                    'website_id' => 1,
                    'dest_country_id' => $data['country'],
                    'dest_region_id' => $regionId,
                    'dest_zip' => $data['zip'],
                    'condition_name' =>'package_value',
                    'condition_value' => $data['order_subtotal'],
                    'price' => $data['price'],
                    'cost' => 0,
                ]
            );
            echo '.';
        }
        $adapter->insert(
            $adapter->getTableName('core_config_data'),
            [
                'scope' => 'default',
                'scope_id' => 0,
                'path' => 'carriers/tablerate/active',
                'value' => 1,
            ]
        );
        $adapter->insert(
            $adapter->getTableName('core_config_data'),
            [
                'scope' => 'default',
                'scope_id' => 0,
                'path' => 'carriers/tablerate/condition_name',
                'value' => 'package_value',
            ]
        );
        $this->cacheTypeList->cleanType('config');
        echo PHP_EOL;
    }

    /**
     * Load directory regions
     *
     * @return array
     */
    protected function loadDirectoryRegions()
    {
        $importRegions = [];
        /** @var $collection \Magento\Directory\Model\Resource\Region\Collection */
        $collection = $this->regionCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }
        return $importRegions;
    }
}
