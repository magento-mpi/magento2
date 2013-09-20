<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Collection
 */
class Magento_Sales_Model_Resource_Sale_Collection extends Magento_Data_Collection_Db
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
     * Customer model
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Order state value
     *
     * @var null|string|array
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
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Sales_Model_Resource_Order
     */
    protected $_orderResource;

    /**
     * @var Magento_Core_Model_Resource_Store_CollectionFactory
     */
    protected $_storeCollFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Set sales order entity and establish read connection
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Sales_Model_Resource_Order $resource
     * @param Magento_Core_Model_Resource_Store_CollectionFactory $storeCollFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @todo: incorrect constructor
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Sales_Model_Resource_Order $resource,
        Magento_Core_Model_Resource_Store_CollectionFactory $storeCollFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_eventManager = $eventManager;
        $this->_orderResource = $resource;
        $this->_storeCollFactory = $storeCollFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($fetchStrategy, $this->_orderResource->getReadConnection());
    }

    /**
     * Set filter by customer
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Sales_Model_Resource_Sale_Collection
     */
    public function setCustomerFilter(Magento_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Add filter by stores
     *
     * @param array $storeIds
     * @return Magento_Sales_Model_Resource_Sale_Collection
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
     * @return Magento_Sales_Model_Resource_Sale_Collection
     */
    public function setOrderStateFilter($state, $exclude = false)
    {
        $this->_orderStateCondition = ($exclude) ? 'NOT IN' : 'IN';
        $this->_orderStateValue     = (!is_array($state)) ? array($state) : $state;
        return $this;
    }


    /**
     * Before load action
     *
     * @return Magento_Data_Collection_Db
     */
    protected function _beforeLoad()
    {
        $this->getSelect()
            ->from(
                array('sales' => $this->_orderResource->getMainTable()),
                array(
                    'store_id',
                    'lifetime'      => new Zend_Db_Expr('SUM(sales.base_grand_total)'),
                    'base_lifetime' => new Zend_Db_Expr('SUM(sales.base_grand_total * sales.base_to_global_rate)'),
                    'avgsale'       => new Zend_Db_Expr('AVG(sales.base_grand_total)'),
                    'base_avgsale'  => new Zend_Db_Expr('AVG(sales.base_grand_total * sales.base_to_global_rate)'),
                    'num_orders'    => new Zend_Db_Expr('COUNT(sales.base_grand_total)')
                )
            )
            ->group('sales.store_id');

        if ($this->_customer instanceof Magento_Customer_Model_Customer) {
            $this->addFieldToFilter('sales.customer_id', $this->_customer->getId());
        }

        if (!is_null($this->_orderStateValue)) {
            $condition = '';
            switch ($this->_orderStateCondition) {
                case 'IN' :
                    $condition = 'in';
                    break;
                case 'NOT IN' :
                    $condition = 'nin';
                    break;
            }
            $this->addFieldToFilter('state', array($condition => $this->_orderStateValue));
        }

        $this->_eventManager->dispatch('sales_sale_collection_query_before', array('collection' => $this));
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return  Magento_Data_Collection_Db
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

        $stores = $this->_storeCollFactory->create()
            ->setWithoutDefaultFilter()
            ->load()
            ->toOptionHash();
        $this->_items = array();
        foreach ($data as $v) {
            $storeObject = new Magento_Object($v);
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
     * Retrieve totals data converted into Magento_Object
     *
     * @return Magento_Object
     */
    public function getTotals()
    {
        return new Magento_Object($this->_totals);
    }
}
