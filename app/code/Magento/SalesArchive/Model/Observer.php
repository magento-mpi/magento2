<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model;

use Magento\Framework\Event\Observer as EventObserver;

/**
 * Order archive observer model
 *
 */
class Observer
{
    /**
     * @var \Magento\SalesArchive\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @var \Magento\SalesArchive\Model\ArchivalList
     */
    protected $_archivalList;

    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * @var \Magento\SalesArchive\Model\Resource\Order\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\SalesArchive\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param \Magento\SalesArchive\Model\ArchiveFactory $archiveFactory
     * @param \Magento\SalesArchive\Model\ArchivalList $archivalList
     * @param \Magento\SalesArchive\Model\Config $config
     * @param \Magento\Backend\Helper\Data $backendData
     */
    public function __construct(
        \Magento\SalesArchive\Model\Resource\Order\CollectionFactory $collectionFactory,
        \Magento\SalesArchive\Model\ArchiveFactory $archiveFactory,
        \Magento\SalesArchive\Model\ArchivalList $archivalList,
        \Magento\SalesArchive\Model\Config $config,
        \Magento\Backend\Helper\Data $backendData
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_backendData = $backendData;
        $this->_archiveFactory = $archiveFactory;
        $this->_archivalList = $archivalList;
        $this->_config = $config;
    }

    /**
     * Get archive instance
     *
     * @return \Magento\SalesArchive\Model\Archive
     */
    protected function _getArchive()
    {
        return $this->_archiveFactory->create();
    }

    /**
     * Archive order by cron
     *
     * @return $this
     */
    public function archiveOrdersByCron()
    {
        if ($this->_config->isArchiveActive()) {
            $this->_getArchive()->archiveOrders();
        }

        return $this;
    }

    /**
     * Mark sales object as archived and set back urls for them
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function salesObjectAfterLoad(EventObserver $observer)
    {
        if (!$this->_config->isArchiveActive()) {
            return $this;
        }
        $object = $observer->getEvent()->getDataObject();
        $archive = $this->_getArchive();
        $archiveEntity = $this->_archivalList->getEntityByObject($object);

        if (!$archiveEntity) {
            return $this;
        }
        $ids = $archive->getIdsInArchive($archiveEntity, $object->getId());
        $object->setIsArchived(!empty($ids));

        if ($object->getIsArchived()) {
            $object->setBackUrl($this->_backendData->getUrl('sales/archive/' . $archiveEntity . 's'));
        } elseif ($object->getIsMoveable() !== false) {
            $object->setIsMoveable(in_array($object->getStatus(), $this->_config->getArchiveOrderStatuses()));
        }
        return $this;
    }

    /**
     * Observes grid records update and depends on data updates records in grid too
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function salesUpdateGridRecords(EventObserver $observer)
    {
        if (!$this->_config->isArchiveActive()) {
            return $this;
        }

        $proxy = $observer->getEvent()->getProxy();

        $archive = $this->_getArchive();
        $archiveEntity = $this->_archivalList->getEntityByObject($proxy->getResource());

        if (!$archiveEntity) {
            return $this;
        }

        $ids = $proxy->getIds();
        $idsInArchive = $archive->getIdsInArchive($archiveEntity, $ids);
        // Exclude archive records from default grid rows update
        $ids = array_diff($ids, $idsInArchive);
        // Check for newly created shipments, creditmemos, invoices
        if ($archiveEntity != \Magento\SalesArchive\Model\ArchivalList::ORDER && !empty($ids)) {
            $relatedIds = $archive->getRelatedIds($archiveEntity, $ids);
            $ids = array_diff($ids, $relatedIds);
            $idsInArchive = array_merge($idsInArchive, $relatedIds);
        }

        $proxy->setIds($ids);

        if (!empty($idsInArchive)) {
            $archive->updateGridRecords($archiveEntity, $idsInArchive);
        }

        return $this;
    }

    /**
     * Add archived orders to order grid collection select
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function appendGridCollection(EventObserver $observer)
    {
        /** @var \Magento\SalesArchive\Model\Resource\Order\Collection $collection */
        $collection = $observer->getEvent()->getOrderGridCollection();
        if ($collection instanceof \Magento\SalesArchive\Model\Resource\Order\Collection ||
            !$collection->getIsCustomerMode()
        ) {
            return $this;
        }

        $collectionSelect = $collection->getSelect();
        $cloneSelect = clone $collectionSelect;

        $union = $this->_collectionFactory->create()->getOrderGridArchiveSelect($cloneSelect);

        /** @var \Magento\Framework\DB\Select $countCloneSelect */
        $countCloneSelect = clone $cloneSelect;
        /** @var \Magento\Framework\DB\Select $countUnionSelect */
        $countUnionSelect = clone $union;
        $countCloneSelect->reset(\Zend_Db_Select::COLUMNS)->columns(['part_total' => 'COUNT(*)']);
        $countUnionSelect->reset(\Zend_Db_Select::COLUMNS)->columns(['part_total' => 'COUNT(*)']);

        $collectionSelect->reset();
        $collectionSelect->union([$countCloneSelect, $countUnionSelect], \Zend_Db_Select::SQL_UNION_ALL);

        $collectionCountSubSelect = clone $collection->getSelect();
        $collectionCountSelect = clone $collectionCountSubSelect;
        $collectionCountSelect->reset();
        // Since UNION ALL leaves duplicates count select can be a sum of counts for 2 parts
        $collectionCountSelect->from(array('u' => $collectionCountSubSelect), 'SUM(part_total)');
        $collection->setSelectCountSql($collectionCountSelect);

        $collectionSelect->reset();
        $collectionSelect->union([$cloneSelect, $union], \Zend_Db_Select::SQL_UNION_ALL);

        return $this;
    }

    /**
     * Replaces redirects to orders list page onto archive orders list page redirects when mass action performed from
     * archive orders list page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function replaceSalesOrderRedirect(EventObserver $observer)
    {
        /**
         * @var \Magento\Backend\App\Action $controller
         */
        $controller = $observer->getControllerAction();
        /**
         * @var \Magento\Framework\App\ResponseInterface $response
         */
        $response = $controller->getResponse();
        /**
         * @var \Magento\Framework\App\RequestInterface $request
         */
        $request = $controller->getRequest();

        if (!$response->isRedirect() || $request->getParam('origin') != 'archive') {
            return $this;
        }

        $ids = $request->getParam('order_ids');
        $createdFromOrders = !empty($ids);

        if ($createdFromOrders) {
            $response->setRedirect($controller->getUrl('sales/archive/orders'));
        } else {
            $response->setRedirect($controller->getUrl('sales/archive/shipments'));
        }
    }
}
