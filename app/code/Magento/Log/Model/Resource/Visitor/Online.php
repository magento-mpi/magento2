<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Prepare Online visitors resource 
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Resource_Visitor_Online extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * @param Magento_Core_Model_Date $date
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Core_Model_Date $date,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_date = $date;
        parent::__construct($resource);
    }

    /**
     * Initialize connection and define resource
     *
     */
    protected function _construct()
    {
        $this->_init('log_visitor_online', 'visitor_id');
    }

    /**
     * Prepare online visitors for collection
     *
     * @param Magento_Log_Model_Visitor_Online $object
     * @return Magento_Log_Model_Resource_Visitor_Online
     */
    public function prepare(Magento_Log_Model_Visitor_Online $object)
    {
        if (($object->getUpdateFrequency() + $object->getPrepareAt()) > time()) {
            return $this;
        }

        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $writeAdapter->beginTransaction();

        try{
            $writeAdapter->delete($this->getMainTable());

            $visitors = array();
            $lastUrls = array();

            // retrieve online visitors general data

            $lastDate = $this->_date->gmtTimestamp() - $object->getOnlineInterval() * 60;

            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log_visitor'),
                    array('visitor_id', 'first_visit_at', 'last_visit_at', 'last_url_id'))
                ->where('last_visit_at >= ?', $readAdapter->formatDate($lastDate));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']] = $row;
                $lastUrls[$row['last_url_id']] = $row['visitor_id'];
                $visitors[$row['visitor_id']]['visitor_type'] = Magento_Log_Model_Visitor::VISITOR_TYPE_VISITOR;
                $visitors[$row['visitor_id']]['customer_id']  = null;
            }

            if (!$visitors) {
                $this->commit();
                return $this;
            }

            // retrieve visitor remote addr
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log_visitor_info'),
                    array('visitor_id', 'remote_addr'))
                ->where('visitor_id IN(?)', array_keys($visitors));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']]['remote_addr'] = $row['remote_addr'];
            }

            // retrieve visitor last URLs
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log_url_info'),
                    array('url_id', 'url'))
                ->where('url_id IN(?)', array_keys($lastUrls));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitorId = $lastUrls[$row['url_id']];
                $visitors[$visitorId]['last_url'] = $row['url'];
            }

            // retrieve customers
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log_customer'),
                    array('visitor_id', 'customer_id'))
                ->where('visitor_id IN(?)', array_keys($visitors));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']]['visitor_type'] = Magento_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER;
                $visitors[$row['visitor_id']]['customer_id']  = $row['customer_id'];
            }

            foreach ($visitors as $visitorData) {
                unset($visitorData['last_url_id']);

                $writeAdapter->insertForce($this->getMainTable(), $visitorData);
            }

            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $object->setPrepareAt();

        return $this;
    }
}
