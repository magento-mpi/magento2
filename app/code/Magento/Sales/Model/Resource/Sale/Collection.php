<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Sale;

use Magento\Core\Model\EntityFactory;
use Magento\Core\Model\Resource\Store\CollectionFactory;
use Magento\Core\Model\StoreManagerInterface;
use Magento\Data\Collection\Db\FetchStrategyInterface;
use Magento\Event\ManagerInterface;
use Magento\Logger;
use Magento\Sales\Model\Resource\Order;

/**
 * Sales Collection
 */
class Collection extends \Magento\Data\Collection\Db
{
    /**
     * Totals data
     *
     * @var array
     */
    protected $_totals = array(
        'lifetime' => 0,
        'base_lifetime' => 0,
        'base_avgsale' => 0,
        'num_orders' => 0
    );

    /**
     * @var int
     */
    protected $_customerId;

    /**
     * Order state value
     *
     * @var null|array
     */
    protected $_state = null;

    /**
     * Order state condition
     *
     * @var string
     */
    protected $_orderStateCondition = null;

    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var Order
     */
    protected $_orderResource;

    /**
     * @var CollectionFactory
     */
    protected $_storeCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Order $resource
     * @param CollectionFactory $storeCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Order $resource,
        CollectionFactory $storeCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_eventManager = $eventManager;
        $this->_orderResource = $resource;
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $this->_orderResource->getReadConnection());
    }

    /**
     * Set filter by customer
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerFilter($customerId)
    {
        $this->_customerId = (int)$customerId;
        return $this;
    }

    /**
     * Add filter by stores
     *
     * @param array $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        return $this->addFieldToFilter('store_id', array('in' => $storeIds));
    }

    /**
     * Set filter by order state
     *
     * @param string|array $state
     * @param bool $exclude
     * @return $this
     */
    public function setOrderStateFilter($state, $exclude = false)
    {
        $this->_orderStateCondition = ($exclude) ? 'NOT IN' : 'IN';
        $this->_state = (!is_array($state)) ? array($state) : $state;
        return $this;
    }

    /**
     * Before load action
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->getSelect()
            ->from(
                array('sales' => $this->_orderResource->getMainTable()),
                array(
                    'store_id',
                    'lifetime'      => new \Zend_Db_Expr('SUM(sales.base_grand_total)'),
                    'base_lifetime' => new \Zend_Db_Expr('SUM(sales.base_grand_total * sales.base_to_global_rate)'),
                    'avgsale'       => new \Zend_Db_Expr('AVG(sales.base_grand_total)'),
                    'base_avgsale'  => new \Zend_Db_Expr('AVG(sales.base_grand_total * sales.base_to_global_rate)'),
                    'num_orders'    => new \Zend_Db_Expr('COUNT(sales.base_grand_total)')
                )
            )
            ->group('sales.store_id');

        if ($this->_customerId) {
            $this->addFieldToFilter('sales.customer_id', $this->_customerId);
        }

        if (!is_null($this->_state)) {
            $condition = '';
            switch ($this->_orderStateCondition) {
                case 'IN' :
                    $condition = 'in';
                    break;
                case 'NOT IN' :
                    $condition = 'nin';
                    break;
            }
            $this->addFieldToFilter('state', array($condition => $this->_state));
        }

        $this->_eventManager->dispatch('sales_sale_collection_query_before', array('collection' => $this));
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_beforeLoad();

        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        $this->printLogQuery($printQuery, $logQuery);

        $data = $this->getData();
        $this->resetData();

        $stores = $this->_storeCollectionFactory->create()
            ->setWithoutDefaultFilter()
            ->load()
            ->toOptionHash();
        $this->_items = array();
        foreach ($data as $v) {
            $storeObject = new \Magento\Object($v);
            $storeId     = $v['store_id'];
            $storeName   = isset($stores[$storeId]) ? $stores[$storeId] : null;
            $storeObject->setStoreName($storeName)
                ->setWebsiteId($this->_storeManager->getStore($storeId)->getWebsiteId())
                ->setAvgNormalized($v['avgsale'] * $v['num_orders']);
            $this->_items[$storeId] = $storeObject;
            foreach ($this->_totals as $key => $value) {
                $this->_totals[$key] += $storeObject->getData($key);
            }
        }

        if ($this->_totals['num_orders']) {
            $this->_totals['avgsale'] = $this->_totals['base_lifetime'] / $this->_totals['num_orders'];
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

    /**
     * Retrieve totals data converted into \Magento\Object
     *
     * @return \Magento\Object
     */
    public function getTotals()
    {
        return new \Magento\Object($this->_totals);
    }
}
