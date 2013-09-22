<?php
/**
 * {license_notice}
 *
 * @category     Magento
 * @package      \Magento\Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with database backups
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup;

class Db extends \Magento\Backup\AbstractBackup
{
    /**
     * @var Magento_Backup_Model_BackupFactory
     */
    protected $_backupFactory;

    /**
     * @var Magento_Backup_Model_DbFactory
     */
    protected $_backupDbFactory;

    /**
     * @param Magento_Backup_Model_BackupFactory $backupFactory
     * @param Magento_Backup_Model_DbFactory $backupDbFactory
     */
    public function __construct(
        Magento_Backup_Model_BackupFactory $backupFactory,
        Magento_Backup_Model_DbFactory $backupDbFactory
    ) {
        $this->_backupFactory = $backupFactory;
        $this->_backupDbFactory = $backupDbFactory;
    }

    /**
     * Implements Rollback functionality for Db
     *
     * @return bool
     */
    public function rollback()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $this->_lastOperationSucceed = false;

        $archiveManager = new \Magento\Archive();
        $source = $archiveManager->unpack($this->getBackupPath(), $this->getBackupsDir());

        $file = new \Magento\Backup\Filesystem\Iterator\File($source);
        foreach ($file as $statement) {
            $this->getResourceModel()->runCommand($statement);
        }
        @unlink($source);

        $this->_lastOperationSucceed = true;

        return true;
    }

    /**
     * Checks whether the line is last in sql command
     *
     * @param $line
     * @return bool
     */
    protected function _isLineLastInCommand($line)
    {
        $cleanLine = trim($line);
        $lineLength = strlen($cleanLine);

        $returnResult = false;
        if ($lineLength > 0) {
            $lastSymbolIndex = $lineLength-1;
            if ($cleanLine[$lastSymbolIndex] == ';') {
                $returnResult = true;
            }
        }

        return $returnResult;
    }

    /**
     * Implements Create Backup functionality for Db
     *
     * @return bool
     */
    public function create()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $this->_lastOperationSucceed = false;

        $backup = $this->_backupFactory
            ->create()
            ->setTime($this->getTime())
            ->setType($this->getType())
            ->setPath($this->getBackupsDir())
            ->setName($this->getName());

        $backupDb = $this->_backupDbFactory->create();
        $backupDb->createBackup($backup);

        $this->_lastOperationSucceed = true;

        return true;
    }

    /**
     * Get Backup Type
     *
     * @return string
     */
    public function getType()
    {
        return 'db';
    }
}
