<?php
/**
 * {license_notice}
 *
 * @category     Magento
 * @package      Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Filesystem\Rollback;

/**
 * Rollback worker for rolling back via ftp
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ftp extends AbstractRollback
{
    /**
     * Ftp client
     *
     * @var \Magento\System\Ftp
     */
    protected $_ftpClient;

    /**
     * Files rollback implementation via ftp
     *
     * @return void
     * @throws \Magento\Exception
     *
     * @see AbstractRollback::run()
     */
    public function run()
    {
        $snapshotPath = $this->_snapshot->getBackupPath();

        if (!is_file($snapshotPath) || !is_readable($snapshotPath)) {
            throw new \Magento\Backup\Exception\CantLoadSnapshot('Cant load snapshot archive');
        }

        $this->_initFtpClient();
        $this->_validateFtp();

        $tmpDir = $this->_createTmpDir();
        $this->_unpackSnapshot($tmpDir);

        $fsHelper = new \Magento\Backup\Filesystem\Helper();

        $this->_cleanupFtp();
        $this->_uploadBackupToFtp($tmpDir);

        $fsHelper->rm($tmpDir, array(), true);
    }

    /**
     * Initialize ftp client and connect to ftp
     *
     * @return void
     * @throws \Magento\Backup\Exception\FtpConnectionFailed
     */
    protected function _initFtpClient()
    {
        try {
            $this->_ftpClient = new \Magento\System\Ftp();
            $this->_ftpClient->connect($this->_snapshot->getFtpConnectString());
        } catch (\Exception $e) {
            throw new \Magento\Backup\Exception\FtpConnectionFailed($e->getMessage());
        }
    }

    /**
     * Perform ftp validation. Check whether ftp account provided points to current magento installation
     *
     * @return void
     * @throws \Magento\Exception
     */
    protected function _validateFtp()
    {
        $validationFilename = '~validation-' . microtime(true) . '.tmp';
        $validationFilePath = $this->_snapshot->getBackupsDir() . '/' . $validationFilename;

        $fh = @fopen($validationFilePath, 'w');
        @fclose($fh);

        if (!is_file($validationFilePath)) {
            throw new \Magento\Exception('Unable to validate ftp account');
        }

        $rootDir = $this->_snapshot->getRootDir();
        $ftpPath = $this->_snapshot->getFtpPath() . '/' . str_replace($rootDir, '', $validationFilePath);

        $fileExistsOnFtp = $this->_ftpClient->fileExists($ftpPath);
        @unlink($validationFilePath);

        if (!$fileExistsOnFtp) {
            throw new \Magento\Backup\Exception\FtpValidationFailed('Failed to validate ftp account');
        }
    }

    /**
     * Unpack snapshot
     *
     * @param string $tmpDir
     * @return void
     */
    protected function _unpackSnapshot($tmpDir)
    {
        $snapshotPath = $this->_snapshot->getBackupPath();

        $archiver = new \Magento\Archive();
        $archiver->unpack($snapshotPath, $tmpDir);
    }

    /**
     * @return string
     * @throws \Magento\Exception
     */
    protected function _createTmpDir()
    {
        $tmpDir = $this->_snapshot->getBackupsDir() . '/~tmp-' . microtime(true);

        $result = @mkdir($tmpDir);

        if (false === $result) {
            throw new \Magento\Backup\Exception\NotEnoughPermissions('Failed to create directory ' . $tmpDir);
        }

        return $tmpDir;
    }

    /**
     * Delete magento and all files from ftp
     *
     * @return void
     */
    protected function _cleanupFtp()
    {
        $rootDir = $this->_snapshot->getRootDir();

        $filesystemIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootDir),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $iterator = new \Magento\Backup\Filesystem\Iterator\Filter(
            $filesystemIterator,
            $this->_snapshot->getIgnorePaths()
        );

        foreach ($iterator as $item) {
            $ftpPath = $this->_snapshot->getFtpPath() . '/' . str_replace($rootDir, '', $item->__toString());
            $ftpPath = str_replace('\\', '/', $ftpPath);

            $this->_ftpClient->delete($ftpPath);
        }
    }

    /**
     * Perform files rollback
     *
     * @param string $tmpDir
     * @return void
     * @throws \Magento\Exception
     */
    protected function _uploadBackupToFtp($tmpDir)
    {
        $filesystemIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tmpDir),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $iterator = new \Magento\Backup\Filesystem\Iterator\Filter(
            $filesystemIterator,
            $this->_snapshot->getIgnorePaths()
        );

        foreach ($filesystemIterator as $item) {
            $ftpPath = $this->_snapshot->getFtpPath() . '/' . str_replace($tmpDir, '', $item->__toString());
            $ftpPath = str_replace('\\', '/', $ftpPath);

            if ($item->isLink()) {
                continue;
            }

            if ($item->isDir()) {
                $this->_ftpClient->mkdirRecursive($ftpPath);
            } else {
                $result = $this->_ftpClient->put($ftpPath, $item->__toString());
                if (false === $result) {
                    throw new \Magento\Backup\Exception\NotEnoughPermissions(
                        'Failed to upload file ' . $item->__toString() . ' to ftp'
                    );
                }
            }
        }
    }
}
