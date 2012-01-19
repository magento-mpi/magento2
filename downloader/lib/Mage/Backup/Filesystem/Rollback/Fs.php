<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rollback worker for rolling back via local filesystem
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Filesystem_Rollback_Fs extends Mage_Backup_Filesystem_Rollback_Abstract
{
    /**
     * Files rollback implementation via local filesystem
     *
     * @see Mage_Backup_Filesystem_Rollback_Abstract::run()
     * @throws Mage_Exception
     */
    public function run()
    {
        $snapshotPath = $this->_snapshot->getBackupPath();

        if (!is_file($snapshotPath) || !is_readable($snapshotPath)) {
            throw new Mage_Backup_Exception_CantLoadSnapshot('Cant load snapshot archive');
        }

        $fsHelper = new Mage_Backup_Filesystem_Helper();

        $filesInfo = $fsHelper->getInfo(
            $this->_snapshot->getRootDir(),
            Mage_Backup_Filesystem_Helper::INFO_WRITABLE,
            $this->_snapshot->getIgnorePaths()
        );

        if (!$filesInfo['writable']) {
            throw new Mage_Backup_Exception_NotEnoughPermissions(
                'Unable to make rollback because not all files are writable'
            );
        }

        $archiver = new Mage_Archive();

        /**
         * we need these fake initializations because all magento's files in filesystem will be deleted and autoloader
         * wont be able to load classes that we need for unpacking
         */
        new Mage_Archive_Tar();
        new Mage_Archive_Gz();
        new Mage_Archive_Helper_File('');
        new Mage_Archive_Helper_File_Gz('');

        $fsHelper->rm($this->_snapshot->getRootDir(), $this->_snapshot->getIgnorePaths());
        $archiver->unpack($snapshotPath, $this->_snapshot->getRootDir());
    }
}
