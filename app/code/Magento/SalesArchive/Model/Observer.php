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
     * Archive model
     * @var Magento_SalesArchive_Model_Archive
     */
    protected $_archive;

    /**
     * Archive config model
     * @var Magento_SalesArchive_Model_Config
     */
    protected $_config;

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData
    ) {
        $this->_backendData = $backendData;
        $this->_archive = Mage::getModel('Magento_SalesArchive_Model_Archive');
        $this->_config  = Mage::getSingleton('Magento_SalesArchive_Model_Config');
    }

    /**
     * Archive order by cron
     *
     * @return Magento_SalesArchive_Model_Observer
     */
    public function archiveOrdersByCron()
    {
        if ($this->_config->isArchiveActive()) {
            $this->_archive->archiveOrders();
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
        $archiveEntity = $this->_archive->detectArchiveEntity($object);

        if (!$archiveEntity) {
            return $this;
        }
        $ids = $this->_archive->getIdsInArchive($archiveEntity, $object->getId());
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

        $archiveEntity = $this->_archive->detectArchiveEntity($proxy->getResource());

        if (!$archiveEntity) {
            return $this;
        }

        $ids = $proxy->getIds();
        $idsInArchive = $this->_archive->getIdsInArchive($archiveEntity, $ids);
        // Exclude archive records from default grid rows update
        $ids = array_diff($ids, $idsInArchive);
        // Check for newly created shipments, creditmemos, invoices
        if ($archiveEntity != Magento_SalesArchive_Model_ArchivalList::ORDER && !empty($ids)) {
            $relatedIds = $this->_archive->getRelatedIds($archiveEntity, $ids);
            $ids = array_diff($ids, $relatedIds);
            $idsInArchive = array_merge($idsInArchive, $relatedIds);
        }

        $proxy->setIds($ids);

        if (!empty($idsInArchive)) {
            $this->_archive->updateGridRecords($archiveEntity, $idsInArchive);
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

        $union = Mage::getResourceModel('Magento_SalesArchive_Model_Resource_Order_Collection')
            ->getOrderGridArchiveSelect($cloneSelect);

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
