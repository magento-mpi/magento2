<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification Inbox model
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Resource_Inbox extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * AdminNotification Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('adminnotification_inbox', 'notification_id');
    }

    /**
     * Load latest notice
     *
     * @param Magento_AdminNotification_Model_Inbox $object
     * @return Magento_AdminNotification_Model_Resource_Inbox
     */
    public function loadLatestNotice(Magento_AdminNotification_Model_Inbox $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->order($this->getIdFieldName() . ' DESC')
            ->where('is_read != 1')
            ->where('is_remove != 1')
            ->limit(1);
        $data = $adapter->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Get notifications grouped by severity
     *
     * @param Magento_AdminNotification_Model_Inbox $object
     * @return array
     */
    public function getNoticeStatus(Magento_AdminNotification_Model_Inbox $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array(
                'severity'     => 'severity',
                'count_notice' => new Zend_Db_Expr('COUNT(' . $this->getIdFieldName() . ')')))
            ->group('severity')
            ->where('is_remove=?', 0)
            ->where('is_read=?', 0);
        $return = $adapter->fetchPairs($select);
        return $return;
    }

    /**
     * Save notifications (if not exists)
     *
     * @param Magento_AdminNotification_Model_Inbox $object
     * @param array $data
     */
    public function parse(Magento_AdminNotification_Model_Inbox $object, array $data)
    {
        $adapter = $this->_getWriteAdapter();
        foreach ($data as $item) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('title = ?', $item['title']);

            if (empty($item['url'])) {
                $select->where('url IS NULL');
            } else {
                $select->where('url = ?', $item['url']);
            }

            if (isset($item['internal'])) {
                $row = false;
                unset($item['internal']);
            } else {
                $row = $adapter->fetchRow($select);
            }

            if (!$row) {
                $adapter->insert($this->getMainTable(), $item);
            }
        }
    }
}
