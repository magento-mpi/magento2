<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log Attempts resource
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Model\Resource;

class Log extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Type Remote Address
     */
    const TYPE_REMOTE_ADDRESS = 1;

    /**
     * Type User Login Name
     */
    const TYPE_LOGIN = 2;

    /**
     * Core Date
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_coreDate;

    /**
     * Core http
     *
     * @var \Magento\Core\Helper\Http
     */
    protected $_coreHttp = null;

    /**
     * @param \Magento\Core\Model\Date $coreDate
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(
        \Magento\Core\Model\Date $coreDate,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Resource $resource
    ) {
        $this->_coreDate = $coreDate;
        $this->_coreHttp = $coreHttp;
        parent::__construct($resource);
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_setMainTable('captcha_log');
    }

    /**
     * Save or Update count Attempts
     *
     * @param string|null $login
     * @return \Magento\Captcha\Model\Resource\Log
     */
    public function logAttempt($login)
    {
        if ($login != null) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => self::TYPE_LOGIN, 'value' => $login, 'count' => 1,
                     'updated_at' => $this->_coreDate->gmtDate()
                ),
                array('count' => new \Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        $ip = $this->_coreHttp->getRemoteAddr();
        if ($ip != null) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => self::TYPE_REMOTE_ADDRESS, 'value' => $ip, 'count' => 1,
                     'updated_at' => $this->_coreDate->gmtDate()
                ),
                array('count' => new \Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        return $this;
    }

    /**
     * Delete User attempts by login
     *
     * @param string $login
     * @return \Magento\Captcha\Model\Resource\Log
     */
    public function deleteUserAttempts($login)
    {
        if ($login != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                array('type = ?' => self::TYPE_LOGIN, 'value = ?' => $login)
            );
        }
        $ip = $this->_coreHttp->getRemoteAddr();
        if ($ip != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(), array('type = ?' => self::TYPE_REMOTE_ADDRESS, 'value = ?' => $ip)
            );
        }

        return $this;
    }

    /**
     * Get count attempts by ip
     *
     * @return null|int
     */
    public function countAttemptsByRemoteAddress()
    {
        $ip = $this->_coreHttp->getRemoteAddr();
        if (!$ip) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), 'count')->where('type = ?', self::TYPE_REMOTE_ADDRESS)
            ->where('value = ?', $ip);
        return $read->fetchOne($select);
    }

    /**
     * Get count attempts by user login
     *
     * @param string $login
     * @return null|int
     */
    public function countAttemptsByUserLogin($login)
    {
        if (!$login) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), 'count')->where('type = ?', self::TYPE_LOGIN)
            ->where('value = ?', $login);
        return $read->fetchOne($select);
    }

    /**
     * Delete attempts with expired in update_at time
     *
     * @return void
     */
    public function deleteOldAttempts()
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('updated_at < ?' => $this->_coreDate->gmtDate(null, time() - 60*30))
        );
    }
}
