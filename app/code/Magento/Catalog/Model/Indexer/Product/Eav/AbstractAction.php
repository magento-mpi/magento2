<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav;

/**
 * Abstract action reindex class
 */
abstract class AbstractAction
{
    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $_resource;

    /**
     * EAV Indexers by type
     *
     * @var array
     */
    protected $_types;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Indexer\Eav\SourceFactory
     */
    protected $_eavSourceFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Indexer\Eav\DecimalFactory
     */
    protected $_eavDecimalFactory;

    /**
     * @var bool
     */
    protected $_isNeedUseIdxTable;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Resource\Product\Indexer\Eav\DecimalFactory $eavDecimalFactory
     * @param \Magento\Catalog\Model\Resource\Product\Indexer\Eav\SourceFactory $eavSourceFactory
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Resource\Product\Indexer\Eav\DecimalFactory $eavDecimalFactory,
        \Magento\Catalog\Model\Resource\Product\Indexer\Eav\SourceFactory $eavSourceFactory,
        \Magento\Framework\Logger $logger
    ) {
        $this->_resource = $resource;
        $this->_storeManager = $storeManager;
        $this->_eavDecimalFactory = $eavDecimalFactory;
        $this->_eavSourceFactory = $eavSourceFactory;
        $this->_logger = $logger;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return void
     */
    abstract public function execute($ids);

    /**
     * Retrieve array of EAV type indexers
     *
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav[]
     */
    public function getIndexers()
    {
        if (is_null($this->_types)) {
            $this->_types = array(
                'source' => $this->_eavSourceFactory->create(),
                'decimal' => $this->_eavDecimalFactory->create()
            );
        }

        return $this->_types;
    }

    /**
     * Retrieve indexer instance by type
     *
     * @param string $type
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav
     * @throws \Magento\Framework\Model\Exception
     */
    public function getIndexer($type)
    {
        $indexers = $this->getIndexers();
        if (!isset($indexers[$type])) {
            throw new \Magento\Framework\Model\Exception(__('Unknown EAV indexer type "%1".', $type));
        }
        return $indexers[$type];
    }

    /**
     * Rebuild all index data
     *
     * @return $this
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        foreach ($this->getIndexers() as $indexer) {
            $indexer->reindexAll();
        }

        return $this;
    }

    /**
     * Retrieve temporary source index table name
     *
     * @return string
     */
    public function getIdxTable()
    {
        if ($this->useIdxTable()) {
            return $this->_getTable('catalog_product_index_eav_idx');
        }
        return $this->_getTable('catalog_product_index_eav_tmp');
    }

    /**
     * Set or get what either "_idx" or "_tmp" suffixed temporary index table need to use
     *
     * @param bool $value
     * @return bool
     */
    public function useIdxTable($value = null)
    {
        if (!is_null($value)) {
            $this->_isNeedUseIdxTable = (bool)$value;
        }
        return $this->_isNeedUseIdxTable;
    }

    /**
     * Returns table name for given entity
     *
     * @param string $entityName
     * @return string
     */
    protected function _getTable($entityName)
    {
        return $this->_resource->getTableName($entityName);
    }
}
