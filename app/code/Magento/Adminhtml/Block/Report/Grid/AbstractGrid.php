<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Adminhtml\Block\Report\Grid;

class AbstractGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_resourceCollectionName  = '';
    protected $_currentCurrencyCode     = null;
    protected $_storeIds                = array();
    protected $_aggregatedColumns       = null;

    /**
     * Reports data
     *
     * @var \Magento\Reports\Helper\Data
     */
    protected $_reportsData = null;

    /**
     * Reports grouped collection factory
     *
     * @var \Magento\Reports\Model\Grouped\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Resource collection factory
     *
     * @var \Magento\Reports\Model\Resource\Report\Collection\Factory
     */
    protected $_resourceFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Reports\Model\Resource\Report\Collection\Factory $resourceFactory
     * @param \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory
     * @param \Magento\Reports\Helper\Data $reportsData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Reports\Model\Resource\Report\Collection\Factory $resourceFactory,
        \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory,
        \Magento\Reports\Helper\Data $reportsData,
        array $data = array()
    ) {
        $this->_resourceFactory = $resourceFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_reportsData = $reportsData;
        parent::__construct($context, $coreData, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        if (isset($this->_columnGroupBy)) {
            $this->isColumnGrouped($this->_columnGroupBy, true);
        }
        $this->setEmptyCellLabel(__('We couldn\'t find records for this period.'));
    }

    public function getResourceCollectionName()
    {
        return $this->_resourceCollectionName;
    }

    public function getCollection()
    {
        if (is_null($this->_collection)) {
            $this->setCollection($this->_collectionFactory->create());
        }
        return $this->_collection;
    }

    protected function _getAggregatedColumns()
    {
        if (is_null($this->_aggregatedColumns)) {
            foreach ($this->getColumns() as $column) {
                if (!is_array($this->_aggregatedColumns)) {
                    $this->_aggregatedColumns = array();
                }
                if ($column->hasTotal()) {
                    $this->_aggregatedColumns[$column->getId()] = "{$column->getTotal()}({$column->getIndex()})";
                }
            }
        }
        return $this->_aggregatedColumns;
    }

    /**
     * Add column to grid
     * Overridden to add support for visibility_filter column option
     * It stands for conditional visibility of the column depending on filter field values
     * Value of visibility_filter supports (filter_field_name => filter_field_value) pairs
     *
     * @param   string $columnId
     * @param   array $column
     * @return  \Magento\Adminhtml\Block\Report\Grid\AbstractGrid
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column) && array_key_exists('visibility_filter', $column)) {
            $filterData = $this->getFilterData();
            $visibilityFilter = $column['visibility_filter'];
            if (!is_array($visibilityFilter)) {
                $visibilityFilter = array($visibilityFilter);
            }
            foreach ($visibilityFilter as $k => $v) {
                if (is_int($k)) {
                    $filterFieldId = $v;
                    $filterFieldValue = true;
                } else {
                    $filterFieldId = $k;
                    $filterFieldValue = $v;
                }
                if (
                    !$filterData->hasData($filterFieldId) ||
                    $filterData->getData($filterFieldId) != $filterFieldValue
                ) {
                    return $this;  // don't add column
                }
            }
        }
        return parent::addColumn($columnId, $column);
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
        $filterData = $this->getFilterData();
        if ($filterData) {
            $storeIds = explode(',', $filterData->getData('store_ids'));
        } else {
            $storeIds = array();
        }
        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys($this->_storeManager->getStores());
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        // If selected all websites or unauthorized stores use only allowed
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values($storeIds);

        return $storeIds;
    }

    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();

        if ($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection();
        }

        $storeIds = $this->_getStoreIds();;

        $orderStatuses = $filterData->getData('order_statuses');
        if (is_array($orderStatuses)) {
            if (count($orderStatuses) == 1 && strpos($orderStatuses[0], ',') !== false) {
                $filterData->setData('order_statuses', explode(',', $orderStatuses[0]));
            }
        }

        $resourceCollection = $this->_resourceFactory->create($this->getResourceCollectionName())
            ->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());

        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);

        if ($this->_isExport) {
            $this->setCollection($resourceCollection);
            return $this;
        }

        if ($filterData->getData('show_empty_rows', false)) {
            $this->_reportsData->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('from', null),
                $filterData->getData('to', null),
                $filterData->getData('period_type')
            );
        }

        if ($this->getCountSubTotals()) {
            $this->getSubTotals();
        }

        if ($this->getCountTotals()) {
            $totalsCollection = $this->_resourceFactory->create($this->getResourceCollectionName())
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter($storeIds)
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isTotals(true);

            $this->_addOrderStatusFilter($totalsCollection, $filterData);
            $this->_addCustomFilter($totalsCollection, $filterData);

            foreach ($totalsCollection as $item) {
                $this->setTotals($item);
                break;
            }
        }

        $this->getCollection()->setColumnGroupBy($this->_columnGroupBy);
        $this->getCollection()->setResourceCollection($resourceCollection);

        return parent::_prepareCollection();
    }

    public function getCountTotals()
    {
        if (!$this->getTotals()) {
            $filterData = $this->getFilterData();
            $totalsCollection = $this->_resourceFactory->create($this->getResourceCollectionName())
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter($this->_getStoreIds())
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isTotals(true);

            $this->_addOrderStatusFilter($totalsCollection, $filterData);

            if (count($totalsCollection->getItems()) < 1 || !$filterData->getData('from')) {
                $this->setTotals(new \Magento\Object());
            } else {
                foreach ($totalsCollection->getItems() as $item) {
                    $this->setTotals($item);
                    break;
                }
            }
        }
        return parent::getCountTotals();
    }

    public function getSubTotals()
    {
        $filterData = $this->getFilterData();
        $subTotalsCollection = $this->_resourceFactory->create($this->getResourceCollectionName())
            ->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($this->_getStoreIds())
            ->setAggregatedColumns($this->_getAggregatedColumns())
            ->isSubTotals(true);

        $this->_addOrderStatusFilter($subTotalsCollection, $filterData);
        $this->_addCustomFilter($subTotalsCollection, $filterData);

        $this->setSubTotals($subTotalsCollection->getItems());
        return parent::getSubTotals();
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? $this->_storeManager->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : $this->_storeManager->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    /**
     * Get currency rate (base to given currency)
     *
     * @param string|\Magento\Directory\Model\Currency $currencyCode
     * @return double
     */
    public function getRate($toCurrency)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->getRate($toCurrency);
    }

    /**
     * Add order status filter
     *
     * @param \Magento\Reports\Model\Resource\Report\Collection\AbstractCollection $collection
     * @param \Magento\Object $filterData
     * @return \Magento\Adminhtml\Block\Report\Grid\AbstractGrid
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        $collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        return $this;
    }

    /**
     * Adds custom filter to resource collection
     * Can be overridden in child classes if custom filter needed
     *
     * @param \Magento\Reports\Model\Resource\Report\Collection\AbstractCollection $collection
     * @param \Magento\Object $filterData
     * @return \Magento\Adminhtml\Block\Report\Grid\AbstractGrid
     */
    protected function _addCustomFilter($collection, $filterData)
    {
        return $this;
    }
}
