<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Category;

use \Magento\Core\Model\Resource\Website\CollectionFactory as WebsiteCollectionFactory;
use \Magento\Customer\Model\Resource\Group\CollectionFactory as GroupCollectionFactory;

abstract class AbstractAction
{
    /**
     * Index table name
     */
    const INDEX_TABLE = 'magento_catalogpermissions_index';

    /**
     * Suffix for table to show it is temporary
     */
    const TABLE_SUFFIX = '_tmp';

    /**
     * Chunk size
     */
    const RANGE_STEP = 100;

    /**
     * @var \Magento\App\Resource
     */
    protected $resource;

    /**
     * @var WebsiteCollectionFactory
     */
    protected $websiteCollectionFactory;

    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var array
     */
    protected $websitesIds = [];

    /**
     * @var array
     */
    protected $customerGroupIds = [];

    /**
     * Whether to use index or temporary index table
     *
     * @var bool
     */
    protected $useTempTable = true;

    /**
     * @param \Magento\App\Resource $resource
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        WebsiteCollectionFactory $websiteCollectionFactory,
        GroupCollectionFactory $groupCollectionFactory
    ) {
        $this->resource = $resource;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * Retrieve connection for read data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function getReadAdapter()
    {
        $writeAdapter = $this->getWriteAdapter();
        if ($writeAdapter && $writeAdapter->getTransactionLevel() > 0) {
            // if transaction is started we should use write connection for reading
            return $writeAdapter;
        }
        return $this->resource->getConnection('read');
    }

    /**
     * Retrieve connection for write data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function getWriteAdapter()
    {
        return $this->resource->getConnection('write');
    }

    /**
     * Return validated table name
     *
     * @param string|string[] $table
     * @return string
     */
    protected function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

    /**
     * Return main index table name
     *
     * @return string
     */
    protected function getIndexTable()
    {
        return $this->getTable(self::INDEX_TABLE);
    }

    /**
     * Retrieve temporary index table name
     *
     * If 'useTempTable' flag is true:
     *  - return temporary index table name.
     *
     * If 'useTempTable' flag is false:
     *  - return index table name.
     *
     * @return string
     */
    protected function getIndexTmpTable()
    {
        return $this->useTempTable
            ? $this->getTable(self::INDEX_TABLE . self::TABLE_SUFFIX)
            : $this->getIndexTable();
    }

    /**
     * Retrieve list of customer group identifiers
     *
     * Return identifiers for all customer groups that exist in the system
     *
     * @return array
     */
    protected function getCustomerGroupIds()
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = $this->groupCollectionFactory->create()
                ->getAllIds();
        }
        return $this->customerGroupIds;
    }

    /**
     * Retrieve list of website identifiers
     *
     * Return identifiers for all websites that exist in the system
     *
     * @return array
     */
    protected function getWebsitesIds()
    {
        if (!$this->websitesIds) {
            $this->websitesIds = $this->websiteCollectionFactory->create()
                ->addFieldToFilter('website_id', ['neq' => 0])
                ->getAllIds();
        }
        return $this->websitesIds;
    }

    /**
     * Check whether select ranging is needed
     *
     * @return bool
     */
    protected function isRangingNeeded()
    {
        return true;
    }

    /**
     * Return selects cut by min and max
     *
     * @param \Magento\DB\Select $select
     * @param string $field
     * @param int $range
     * @return \Magento\DB\Select[]
     */
    protected function prepareSelectsByRange(\Magento\DB\Select $select, $field, $range = self::RANGE_STEP)
    {
        return $this->isRangingNeeded()
            ? $this->getWriteAdapter()->selectsByRange($field, $select, $range)
            : [$select];
    }
}
