<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SendFriend Log Resource Model
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Model\Resource;

class Sendfriend extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize connection and table
     *
     */
    protected function _construct()
    {
        $this->_init('sendfriend_log', 'log_id');
    }

    /**
     * Retrieve Sended Emails By Ip
     *
     * @param \Magento\Sendfriend\Model\Sendfriend $object
     * @param int $ip
     * @param int $startTime
     * @param int $websiteId
     * @return int
     */
    public function getSendCount($object, $ip, $startTime, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('count' => new \Zend_Db_Expr('count(*)')))
            ->where('ip=:ip
                AND  time>=:time
                AND  website_id=:website_id');
        $bind = array(
            'ip'      => $ip,
            'time'    => $startTime,
            'website_id' => (int)$websiteId,
        );

        $row = $adapter->fetchRow($select, $bind);
        return $row['count'];
    }

    /**
     * Add sended email by ip item
     *
     * @param int $ip
     * @param int $startTime
     * @param int $websiteId
     * @return \Magento\Sendfriend\Model\Resource\Sendfriend
     */
    public function addSendItem($ip, $startTime, $websiteId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getMainTable(),
            array(
                'ip'         => $ip,
                'time'       => $startTime,
                'website_id' => $websiteId
             )
        );
        return $this;
    }

    /**
     * Delete Old logs
     *
     * @param int $time
     * @return \Magento\Sendfriend\Model\Resource\Sendfriend
     */
    public function deleteLogsBefore($time)
    {
        $cond = $this->_getWriteAdapter()->quoteInto('time<?', $time);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $cond);

        return $this;
    }
}
