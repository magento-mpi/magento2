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
 * Class to work with archives
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Backup_Abstract implements  Mage_Backup_Interface
{
    /**
     * Backup creation date
     *
     * @var int
     */
    protected $_time;

    /**
     * Backup file extension
     *
     * @var string
     */
    protected $_backupExtension;

    /**
     * Resource model
     *
     * @var object
     */
    protected $_resourceModel;

    /**
     * Magento's root directory
     *
     * @var string
     */
    protected $_rootDir;

    /**
     * Path to directory where backups stored
     *
     * @var string
     */
    protected $_backupsDir;

    /**
     * Is last operation completed successfully
     *
     * @var bool
     */
    protected $_lastOperationSucceed = false;


    /**
     * Set Backup Extension
     *
     * @param string $backupExtension
     * @return Mage_Backup_Interface
     */
    public function setBackupExtension($backupExtension)
    {
        $this->_backupExtension = $backupExtension;
        return $this;
    }

    /**
     * Get Backup Extension
     *
     * @return string
     */
    public function getBackupExtension()
    {
        return $this->_backupExtension;
    }

    /**
     * Set Resource Model
     *
     * @param object $resourceModel
     * @return Mage_Backup_Interface
     */
    public function setResourceModel($resourceModel)
    {
        $this->_resourceModel = $resourceModel;
        return $this;
    }

    /**
     * Get Resource Model
     *
     * @return object
     */
    public function getResourceModel()
    {
        return $this->_resourceModel;
    }

    /**
     * Set Time
     *
     * @param int $time
     * @return Mage_Backup_Interface
     */
    public function setTime($time)
    {
        $this->_time = $time;
        return $this;
    }

    /**
     * Get Time
     *
     * @return int
     */
    public function getTime()
    {
        return $this->_time;
    }

    /**
     * Set root directory of Magento installation
     *
     * @param string $rootDir
     * @throws Mage_Exception
     * @return Mage_Backup_Interface
     */
    public function setRootDir($rootDir)
    {
        if (!is_dir($rootDir)) {
            throw new Mage_Exception('Bad root directory');
        }

        $this->_rootDir = $rootDir;
        return $this;
    }

    /**
     * Get Magento's root directory
     * @return string
     */
    public function getRootDir()
    {
        return $this->_rootDir;
    }

    /**
     * Set path to directory where backups stored
     *
     * @param string $backupsDir
     * @return Mage_Backup_Interface
     */
    public function setBackupsDir($backupsDir)
    {
        $this->_backupsDir = $backupsDir;
        return $this;
    }

    /**
     * Get path to directory where backups stored
     *
     * @return string
     */
    public function getBackupsDir()
    {
        return $this->_backupsDir;
    }

    /**
     * Get path to backup
     *
     * @return string
     */
    public function getBackupPath()
    {
        return $this->getBackupsDir() . DS . $this->getBackupFilename();
    }

    /**
     * Get backup file name
     *
     * @return string
     */
    public function getBackupFilename()
    {
        return $this->getTime() . '_' . $this->getType() . '.' . $this->getBackupExtension();
    }

    /**
     * Check whether last operation completed successfully
     *
     * @return bool
     */
    public function getIsSuccess()
    {
        return $this->_lastOperationSucceed;
    }
}
