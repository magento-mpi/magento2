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
 *
 * @category   Magento
 * @package    \Magento\Backup
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * Create Backup
     *
     * @return \Magento\Log\Model\Cron
     */
    public function scheduledBackup()
    {
        if (!\Mage::getStoreConfigFlag(self::XML_PATH_BACKUP_ENABLED)) {
            return $this;
        }

        if (\Mage::getStoreConfigFlag(self::XML_PATH_BACKUP_MAINTENANCE_MODE)) {
            \Mage::helper('Magento\Backup\Helper\Data')->turnOnMaintenanceMode();
        }

        $type = \Mage::getStoreConfig(self::XML_PATH_BACKUP_TYPE);

        $this->_errors = array();
        try {
            $backupManager = \Magento\Backup::getBackupInstance($type)
                ->setBackupExtension(\Mage::helper('Magento\Backup\Helper\Data')->getExtensionByType($type))
                ->setTime(time())
                ->setBackupsDir(\Mage::helper('Magento\Backup\Helper\Data')->getBackupsDir());

            \Mage::register('backup_manager', $backupManager);

            if ($type != \Magento\Backup\Helper\Data::TYPE_DB) {
                $backupManager->setRootDir(\Mage::getBaseDir())
                    ->addIgnorePaths(\Mage::helper('Magento\Backup\Helper\Data')->getBackupIgnorePaths());
            }

            $backupManager->create();
            \Mage::log(\Mage::helper('Magento\Backup\Helper\Data')->getCreateSuccessMessageByType($type));
        }
        catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
            \Mage::log($e->getMessage(), \Zend_Log::ERR);
            \Mage::logException($e);
        }

        if (\Mage::getStoreConfigFlag(self::XML_PATH_BACKUP_MAINTENANCE_MODE)) {
            \Mage::helper('Magento\Backup\Helper\Data')->turnOffMaintenanceMode();
        }

        return $this;
    }
}
