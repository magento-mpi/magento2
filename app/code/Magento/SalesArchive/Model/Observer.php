<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order archive observer model
 *
 */
class Magento_SalesArchive_Model_Observer
{
    /**
     * @var Magento_SalesArchive_Model_ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @var Magento_SalesArchive_Model_ArchivalList
     */
    protected $_archivalList;

    /**
     * @var Magento_SalesArchive_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData;

    /**
     * @var Magento_SalesArchive_Model_Resource_Order_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_SalesArchive_Model_Resource_Order_CollectionFactory $collectionFactory
     * @param Magento_SalesArchive_Model_ArchiveFactory $archiveFactory
     * @param Magento_SalesArchive_Model_ArchivalList $archivalList
     * @param Magento_SalesArchive_Model_Config $config
     * @param Magento_Backend_Helper_Data $backendData
     */
    public function __construct(
        Magento_SalesArchive_Model_Resource_Order_CollectionFactory $collectionFactory,
        Magento_SalesArchive_Model_ArchiveFactory $archiveFactory,
        Magento_SalesArchive_Model_ArchivalList $archivalList,
        Magento_SalesArchive_Model_Config $config,
        Magento_Backend_Helper_Data $backendData
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
     * @return Magento_SalesArchive_Model_Archive
     */
    protected function _getArchive()
    {
        return $this->_archiveFactory->create();
    }

    /**
     * Archive order by cron
     *
     * @return Magento_SalesArchive_Model_Observer
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
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesArchive_Model_Observer
     */
    public function salesObjectAfterLoad(Magento_Event_Observer $observer)
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
            $object->setBackUrl(
                $this->_backendData->getUrl('adminhtml/sales_archive/' . $archiveEntity . 's')
            );
        } elseif ($object->getIsMoveable() !== false) {
            $object->setIsMoveable(
                in_array($object->getStatus(), $this->_config->getArchiveOrderStatuses())
            );
        }
        return $this;
    }

    /**
     * Observes grid records update and depends on data updates records in grid too
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesArchive_Model_Observer
     */
    public function salesUpdateGridRecords(Magento_Event_Observer $observer)
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
        if ($archiveEntity != Magento_SalesArchive_Model_ArchivalList::ORDER && !empty($ids)) {
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
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesArchive_Model_Observer
     */
    public function appendGridCollection(Magento_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderGridCollection();
        if ($collection instanceof Magento_SalesArchive_Model_Resource_Order_Collection
            || !$collection->getIsCustomerMode()) {
            return $this;
        }

        $collectionSelect = $collection->getSelect();
        $cloneSelect = clone $collectionSelect;

        $union = $this->_collectionFactory->create()->getOrderGridArchiveSelect($cloneSelect);

        $unionParts = array($cloneSelect, $union);

        $collectionSelect->reset();
        $collectionSelect->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);

        return $this;
    }

    /**
     * Replaces redirects to orders list page onto archive orders list page redirects when mass action performed from
     * archive orders list page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesArchive_Model_Observer
     */
    public function replaceSalesOrderRedirect(Magento_Event_Observer $observer)
    {
        /**
         * @var Magento_Adminhtml_Controller_Action $controller
         */
        $controller = $observer->getControllerAction();
        /**
         * @var Magento_Core_Controller_Response_Http $response
         */
        $response = $controller->getResponse();
        /**
         * @var Magento_Core_Controller_Request_Http $request
         */
        $request = $controller->getRequest();

        if (!$response->isRedirect() || $request->getParam('origin') != 'archive') {
            return $this;
        }

        $ids = $request->getParam('order_ids');
        $createdFromOrders = !empty($ids);

        if ($createdFromOrders) {
            $response->setRedirect($controller->getUrl('*/sales_archive/orders'));
        } else {
            $response->setRedirect($controller->getUrl('*/sales_archive/shipments'));
        }
    }
}
