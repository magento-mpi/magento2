<?php
/**
 * URL rewrite collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model\Resource;

class UrlRewriteCollection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Store Manager Model
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var []
     */
    protected $indexFields;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\UrlRewrite\Model\UrlRewrite', 'Magento\UrlRewrite\Model\Resource\UrlRewrite');
    }

    /**
     * Filter collections by stores
     *
     * @param mixed $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!is_array($store)) {
            $store = array($this->storeManager->getStore($store)->getId());
        }
        if ($withAdmin) {
            $store[] = 0;
        }

        $this->addFieldToFilter('store_id', array('in' => $store));

        return $this;
    }

    /**
     * Retrieve records by filter
     *
     * @param array $params
     * @return array
     */
    public function searchByParams(array $params)
    {
        $select = $this->getSelect()->reset()->from($this->getMainTable());
        $conditions = $this->prepareParams($params);
        foreach ($conditions as $condition) {
            $select->orWhere(implode(' ', $condition));
        }

        return $select->query()->fetchAll();
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function prepareParams($params)
    {
        $conditions = [];
        $select = clone $this->getSelect();
        $select->reset();
        foreach ($params as $field => $value) {
            if (is_array($value)) {
                $conditions = array_merge($conditions, $this->prepareParams($value));
                continue;
            }
            $select->where($this->getConnection()->quoteIdentifier($field) . " = ?", $value, true);
        }

        $conditions = $select->getPart(\Magento\Framework\DB\Select::WHERE)
            ? array_merge($conditions, [$select->getPart(\Magento\Framework\DB\Select::WHERE)])
            : $conditions;

        return $conditions;
    }
}
