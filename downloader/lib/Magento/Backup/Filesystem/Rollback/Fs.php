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
 * Rollback worker for rolling back via local filesystem
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Filesystem\Rollback;

class Fs extends \Magento\Backup\Filesystem\Rollback\AbstractRollback
{
    /**
     * Files rollback implementation via local filesystem
     *
     * @see \Magento\Backup\Filesystem\Rollback\AbstractRollback::run()
     * @throws \Magento\Exception
     */
    public function run()
    {
        $snapshotPath = $this->_snapshot->getBackupPath();

        if (!is_file($snapshotPath) || !is_readable($snapshotPath)) {
            throw new \Magento\Backup\Exception\CantLoadSnapshot('Cant load snapshot archive');
        }

        $fsHelper = new \Magento\Backup\Filesystem\Helper();

        $filesInfo = $fsHelper->getInfo(
            $this->_snapshot->getRootDir(),
            \Magento\Backup\Filesystem\Helper::INFO_WRITABLE,
            $this->_snapshot->getIgnorePaths()
        );

        if (!$filesInfo['writable']) {
            throw new \Magento\Backup\Exception\NotEnoughPermissions(
                'Unable to make rollback because not all files are writable'
            );
        }

        $archiver = new \Magento\Archive();

        /**
         * we need these fake initializations because all magento's files in filesystem will be deleted and autoloader
         * wont be able to load classes that we need for unpacking
         */
        new \Magento\Archive\Tar();
        new \Magento\Archive\Gz();
        new \Magento\Archive\Helper\File('');
        new \Magento\Archive\Helper\File\Gz('');

        $fsHelper->rm($this->_snapshot->getRootDir(), $this->_snapshot->getIgnorePaths());
        $archiver->unpack($snapshotPath, $this->_snapshot->getRootDir());
    }
}
