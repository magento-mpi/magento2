<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup Observer
 */
namespace Magento\Backup\Model;

class Observer
{
    const XML_PATH_BACKUP_ENABLED          = 'system/backup/enabled';
    const XML_PATH_BACKUP_TYPE             = 'system/backup/type';
    const XML_PATH_BACKUP_MAINTENANCE_MODE = 'system/backup/maintenance';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Backup data
     *
     * @var \Magento\Backup\Helper\Data
     */
    protected $_backupData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Directory model
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * Construct
     * 
     * @param \Magento\Backup\Helper\Data $backupData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Dir $dir
     */
    public function __construct(
        \Magento\Backup\Helper\Data $backupData,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Dir $dir
    ) {
        $this->_backupData = $backupData;
        $this->_coreRegistry = $coreRegistry;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_dir = $dir;
    }

    /**
     * Create Backup
     *
     * @return \Magento\Log\Model\Cron
     */
    public function scheduledBackup()
    {
        if (!$this->_coreStoreConfig->getConfigFlag(self::XML_PATH_BACKUP_ENABLED)) {
            return $this;
        }

        if ($this->_coreStoreConfig->getConfigFlag(self::XML_PATH_BACKUP_MAINTENANCE_MODE)) {
            $this->_backupData->turnOnMaintenanceMode();
        }

        $type = $this->_coreStoreConfig->getConfig(self::XML_PATH_BACKUP_TYPE);

        $this->_errors = array();
        try {
            $backupManager = \Magento\Backup::getBackupInstance($type)
                ->setBackupExtension($this->_backupData->getExtensionByType($type))
                ->setTime(time())
                ->setBackupsDir($this->_backupData->getBackupsDir());

            $this->_coreRegistry->register('backup_manager', $backupManager);

            if ($type != \Magento\Backup\Helper\Data::TYPE_DB) {
                $backupManager->setRootDir($this->_dir->getDir())
                    ->addIgnorePaths($this->_backupData->getBackupIgnorePaths());
            }

            $backupManager->create();
            \Mage::log($this->_backupData->getCreateSuccessMessageByType($type));
        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
            \Mage::log($e->getMessage(), \Zend_Log::ERR);
            \Mage::logException($e);
        }

        if ($this->_coreStoreConfig->getConfigFlag(self::XML_PATH_BACKUP_MAINTENANCE_MODE)) {
            $this->_backupData->turnOffMaintenanceMode();
        }

        return $this;
    }
}
