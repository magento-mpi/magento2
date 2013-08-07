<?php
/**
 * {license_notice}
 *
 * @category     Magento
 * @package      Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rollback worker for rolling back via ftp
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Filesystem_Rollback_Ftp extends Magento_Backup_Filesystem_Rollback_Abstract
{
    /**
     * Ftp client
     *
     * @var Magento_System_Ftp
     */
    protected $_ftpClient;

    /**
     * Files rollback implementation via ftp
     *
     * @see Magento_Backup_Filesystem_Rollback_Abstract::run()
     * @throws Magento_Exception
     */
    public function run()
    {
        $snapshotPath = $this->_snapshot->getBackupPath();

        if (!is_file($snapshotPath) || !is_readable($snapshotPath)) {
            throw new Magento_Backup_Exception_CantLoadSnapshot('Cant load snapshot archive');
        }

        $this->_initFtpClient();
        $this->_validateFtp();

        $tmpDir = $this->_createTmpDir();
        $this->_unpackSnapshot($tmpDir);

        $fsHelper = new Magento_Backup_Filesystem_Helper();

        $this->_cleanupFtp();
        $this->_uploadBackupToFtp($tmpDir);

        $fsHelper->rm($tmpDir, array(), true);
    }

    /**
     * Initialize ftp client and connect to ftp
     *
     * @throws Magento_Backup_Exception_FtpConnectionFailed
     */
    protected function _initFtpClient()
    {
        try {
            $this->_ftpClient = new Magento_System_Ftp();
            $this->_ftpClient->connect($this->_snapshot->getFtpConnectString());
        } catch (Exception $e) {
            throw new Magento_Backup_Exception_FtpConnectionFailed($e->getMessage());
        }
    }

    /**
     * Perform ftp validation. Check whether ftp account provided points to current magento installation
     *
     * @throws Magento_Exception
     */
    protected function _validateFtp()
    {
        $validationFilename = '~validation-' . microtime(true) . '.tmp';
        $validationFilePath = $this->_snapshot->getBackupsDir() . DS . $validationFilename;

        $fh = @fopen($validationFilePath, 'w');
        @fclose($fh);

        if (!is_file($validationFilePath)) {
            throw new Magento_Exception('Unable to validate ftp account');
        }

        $rootDir = $this->_snapshot->getRootDir();
        $ftpPath = $this->_snapshot->getFtpPath() . DS . str_replace($rootDir, '', $validationFilePath);

        $fileExistsOnFtp = $this->_ftpClient->fileExists($ftpPath);
        @unlink($validationFilePath);

        if (!$fileExistsOnFtp) {
            throw new Magento_Backup_Exception_FtpValidationFailed('Failed to validate ftp account');
        }
    }

    /**
     * Unpack snapshot
     *
     * @param string $tmpDir
     */
    protected function _unpackSnapshot($tmpDir)
    {
        $snapshotPath = $this->_snapshot->getBackupPath();

        $archiver = new Magento_Archive();
        $archiver->unpack($snapshotPath, $tmpDir);
    }

    /**
     * @throws Magento_Exception
     * @return string
     */
    protected function _createTmpDir()
    {
        $tmpDir = $this->_snapshot->getBackupsDir() . DS . '~tmp-' . microtime(true);

        $result = @mkdir($tmpDir);

        if (false === $result) {
            throw new Magento_Backup_Exception_NotEnoughPermissions('Failed to create directory ' . $tmpDir);
        }

        return $tmpDir;
    }

    /**
     * Delete magento and all files from ftp
     */
    protected function _cleanupFtp()
    {
        $rootDir = $this->_snapshot->getRootDir();

        $filesystemIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootDir), RecursiveIteratorIterator::CHILD_FIRST
        );

        $iterator = new Magento_Backup_Filesystem_Iterator_Filter($filesystemIterator, $this->_snapshot->getIgnorePaths());

        foreach ($iterator as $item) {
            $ftpPath = $this->_snapshot->getFtpPath() . DS . str_replace($rootDir, '', $item->__toString());
            $ftpPath = str_replace(DS, '/', $ftpPath);

            $this->_ftpClient->delete($ftpPath);
        }
    }

    /**
     * Perform files rollback
     *
     * @param string $tmpDir
     * @throws Magento_Exception
     */
    protected function _uploadBackupToFtp($tmpDir)
    {
        $filesystemIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tmpDir), RecursiveIteratorIterator::SELF_FIRST
        );

        $iterator = new Magento_Backup_Filesystem_Iterator_Filter($filesystemIterator, $this->_snapshot->getIgnorePaths());

        foreach ($filesystemIterator as $item) {
            $ftpPath = $this->_snapshot->getFtpPath() . DS . str_replace($tmpDir, '', $item->__toString());
            $ftpPath = str_replace(DS, '/', $ftpPath);

            if ($item->isLink()) {
                continue;
            }

            if ($item->isDir()) {
                $this->_ftpClient->mkdirRecursive($ftpPath);
            } else {
                $result = $this->_ftpClient->put($ftpPath, $item->__toString());
                if (false === $result) {
                    throw new Magento_Backup_Exception_NotEnoughPermissions('Failed to upload file '
                        . $item->__toString() . ' to ftp');
                }
            }
        }
    }
}
