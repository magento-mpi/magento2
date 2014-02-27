<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Model\Resource;

/**
 * Log Attempts resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
     * @var \Magento\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\Date $coreDate
     * @param \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Model\Date $coreDate,        
        \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->_coreDate = $coreDate;
        $this->_remoteAddress = $remoteAddress;
        parent::__construct($resource);
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setMainTable('captcha_log');
    }

    /**
     * Save or Update count Attempts
     *
     * @param string|null $login
     * @return $this
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
        $ip = $this->_remoteAddress->getRemoteAddress();
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
     * @return $this
     */
    public function deleteUserAttempts($login)
    {
        if ($login != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                array('type = ?' => self::TYPE_LOGIN, 'value = ?' => $login)
            );
        }
        $ip = $this->_remoteAddress->getRemoteAddress();
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
        $ip = $this->_remoteAddress->getRemoteAddress();
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
