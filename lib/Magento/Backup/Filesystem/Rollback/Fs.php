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
class Magento_Backup_Filesystem_Rollback_Fs extends Magento_Backup_Filesystem_Rollback_Abstract
{
    /**
     * Files rollback implementation via local filesystem
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

        $fsHelper = new Magento_Backup_Filesystem_Helper();

        $filesInfo = $fsHelper->getInfo(
            $this->_snapshot->getRootDir(),
            Magento_Backup_Filesystem_Helper::INFO_WRITABLE,
            $this->_snapshot->getIgnorePaths()
        );

        if (!$filesInfo['writable']) {
            throw new Magento_Backup_Exception_NotEnoughPermissions(
                'Unable to make rollback because not all files are writable'
            );
        }

        $archiver = new Magento_Archive();

        /**
         * we need these fake initializations because all magento's files in filesystem will be deleted and autoloader
         * wont be able to load classes that we need for unpacking
         */
        new Magento_Archive_Tar();
        new Magento_Archive_Gz();
        new Magento_Archive_Helper_File('');
        new Magento_Archive_Helper_File_Gz('');

        $fsHelper->rm($this->_snapshot->getRootDir(), $this->_snapshot->getIgnorePaths());
        $archiver->unpack($snapshotPath, $this->_snapshot->getRootDir());
    }
}
