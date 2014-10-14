<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Resource;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Logging event resource model
 */
class Event extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $directory;

    /**
     * Archive factory
     *
     * @var \Magento\Logging\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Logging\Model\ArchiveFactory $archiveFactory
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Logging\Model\ArchiveFactory $archiveFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        parent::__construct($resource);
        $this->_archiveFactory = $archiveFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_logging_event', 'log_id');
    }

    /**
     * Convert data before save ip
     *
     * @param \Magento\Framework\Model\AbstractModel $event
     * @return $this|\Magento\Framework\Model\Resource\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $event)
    {
        $event->setData('ip', ip2long($event->getIp()));
        $event->setTime($this->dateTime->formatDate($event->getTime()));
        return $this;
    }

    /**
     * Rotate logs - get from database and pump to CSV-file
     *
     * @param int $lifetime
     * @return void
     */
    public function rotate($lifetime)
    {
        $readAdapter = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();

        // get the latest log entry required to the moment
        $clearBefore = $this->dateTime->formatDate(time() - $lifetime);

        $select = $readAdapter->select()->from(
            $this->getMainTable(),
            'log_id'
        )->where(
            'time < ?',
            $clearBefore
        )->order(
            'log_id DESC'
        )->limit(
            1
        );
        $latestLogEntry = $readAdapter->fetchOne($select);
        if ($latestLogEntry) {
            // make sure folder for dump file will exist
            /** @var \Magento\Logging\Model\Archive $archive */
            $archive = $this->_archiveFactory->create();
            $archive->createNew();

            $expr = new \Zend_Db_Expr('INET_NTOA(' . $this->_getReadAdapter()->quoteIdentifier('ip') . ')');
            $select = $readAdapter->select()->from(
                $this->getMainTable()
            )->where(
                'log_id <= ?',
                $latestLogEntry
            )->columns(
                $expr
            );

            $rows = $readAdapter->fetchAll($select);

            $path = $this->directory->getRelativePath($archive->getFilename());
            $stream = $this->directory->openFile($path, 'w');
            // dump all records before this log entry into a CSV-file
            foreach ($rows as $row) {
                $stream->writeCsv($row);
            }
            $stream->close();

            $writeAdapter->delete($this->getMainTable(), array('log_id <= ?' => $latestLogEntry));
        }
    }

    /**
     * Select all values of specified field from main table
     *
     * @param string $field
     * @param bool $order
     * @return array
     */
    public function getAllFieldValues($field, $order = true)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->distinct(true)->from($this->getMainTable(), $field);
        if (!is_null($order)) {
            $select->order($field . ($order ? '' : ' DESC'));
        }
        return $adapter->fetchCol($select);
    }

    /**
     * Get all admin user names that are currently in event log table
     * Possible SQL-performance issue
     *
     * @return array
     */
    public function getUserNames()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->distinct()->from(
            array('admins' => $this->getTable('admin_user')),
            'username'
        )->joinInner(
            array('events' => $this->getTable('magento_logging_event')),
            'admins.username = events.' . $adapter->quoteIdentifier('user'),
            array()
        );
        return $adapter->fetchCol($select);
    }

    /**
     * Get event change ids of specified event
     *
     * @param int $eventId
     * @return array
     */
    public function getEventChangeIds($eventId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getTable('magento_logging_event_changes'),
            array('id')
        )->where(
            'event_id = ?',
            $eventId
        );
        return $adapter->fetchCol($select);
    }
}
