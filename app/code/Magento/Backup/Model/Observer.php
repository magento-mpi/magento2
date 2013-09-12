<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backup Observer
 *
 * @category   Magento
 * @package    Magento_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Model_Observer
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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Create Backup
     *
     * @return Magento_Log_Model_Cron
     */
    public function scheduledBackup()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_BACKUP_ENABLED)) {
            return $this;
        }

        if (Mage::getStoreConfigFlag(self::XML_PATH_BACKUP_MAINTENANCE_MODE)) {
            Mage::helper('Magento_Backup_Helper_Data')->turnOnMaintenanceMode();
        }

        $type = Mage::getStoreConfig(self::XML_PATH_BACKUP_TYPE);

        $this->_errors = array();
        try {
            $backupManager = Magento_Backup::getBackupInstance($type)
                ->setBackupExtension(Mage::helper('Magento_Backup_Helper_Data')->getExtensionByType($type))
                ->setTime(time())
                ->setBackupsDir(Mage::helper('Magento_Backup_Helper_Data')->getBackupsDir());

            $this->_coreRegistry->register('backup_manager', $backupManager);

            if ($type != Magento_Backup_Helper_Data::TYPE_DB) {
                $backupManager->setRootDir(Mage::getBaseDir())
                    ->addIgnorePaths(Mage::helper('Magento_Backup_Helper_Data')->getBackupIgnorePaths());
            }

            $backupManager->create();
            Mage::log(Mage::helper('Magento_Backup_Helper_Data')->getCreateSuccessMessageByType($type));
        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
            Mage::log($e->getMessage(), Zend_Log::ERR);
            Mage::logException($e);
        }

        if (Mage::getStoreConfigFlag(self::XML_PATH_BACKUP_MAINTENANCE_MODE)) {
            Mage::helper('Magento_Backup_Helper_Data')->turnOffMaintenanceMode();
        }

        return $this;
    }
}
