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
 * Backup data helper
 */
class Magento_Backup_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Backup type constant for database backup
     */
    const TYPE_DB = 'db';

    /**
     * Backup type constant for filesystem backup
     */
    const TYPE_FILESYSTEM = 'filesystem';

    /**
     * Backup type constant for full system backup(database + filesystem)
     */
    const TYPE_SYSTEM_SNAPSHOT = 'snapshot';

    /**
     * Backup type constant for media and database backup
     */
    const TYPE_MEDIA = 'media';

    /**
     * Backup type constant for full system backup excluding media folder
     */
    const TYPE_SNAPSHOT_WITHOUT_MEDIA = 'nomedia';

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Filesystem $filesystem
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Filesystem $filesystem,
        Magento_AuthorizationInterface $authorization
    ) {
        parent::__construct($context);
        $this->_authorization = $authorization;
        $this->_filesystem = $filesystem;
    }

    /**
     * Get all possible backup type values with descriptive title
     *
     * @return array
     */
    public function getBackupTypes()
    {
        return array(
            self::TYPE_DB                     => __('Database'),
            self::TYPE_MEDIA                  => __('Database and Media'),
            self::TYPE_SYSTEM_SNAPSHOT        => __('System'),
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA => __('System (excluding Media)')
        );
    }

    /**
     * Get all possible backup type values
     *
     * @return array
     */
    public function getBackupTypesList()
    {
        return array(
            self::TYPE_DB,
            self::TYPE_SYSTEM_SNAPSHOT,
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA,
            self::TYPE_MEDIA
        );
    }

    /**
     * Get default backup type value
     *
     * @return string
     */
    public function getDefaultBackupType()
    {
        return self::TYPE_DB;
    }

    /**
     * Get directory path where backups stored
     *
     * @return string
     */
    public function getBackupsDir()
    {
        return Mage::getBaseDir('var') . DS . 'backups';
    }

    /**
     * Get backup file extension by backup type
     *
     * @param string $type
     * @return string
     */
    public function getExtensionByType($type)
    {
        $extensions = $this->getExtensions();
        return isset($extensions[$type]) ? $extensions[$type] : '';
    }

    /**
     * Get all types to extensions map
     *
     * @return array
     */
    public function getExtensions()
    {
        return array(
            self::TYPE_SYSTEM_SNAPSHOT => 'tgz',
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA => 'tgz',
            self::TYPE_MEDIA => 'tgz',
            self::TYPE_DB => 'gz'
        );
    }

    /**
     * Generate backup download name
     *
     * @param Magento_Backup_Model_Backup $backup
     * @return string
     */
    public function generateBackupDownloadName(Magento_Backup_Model_Backup $backup)
    {
        $additionalExtension = $backup->getType() == self::TYPE_DB ? '.sql' : '';
        return $backup->getType() . '-' . date('YmdHis', $backup->getTime()) . $additionalExtension . '.'
            . $this->getExtensionByType($backup->getType());
    }

    /**
     * Check Permission for Rollback
     *
     * @return boolean
     */
    public function isRollbackAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Backup::rollback' );
    }

    /**
     * Get paths that should be ignored when creating system snapshots
     *
     * @return array
     */
    public function getBackupIgnorePaths()
    {
        return array(
            '.git',
            '.svn',
            'maintenance.flag',
            Mage::getBaseDir('var') . DS . 'session',
            Mage::getBaseDir('var') . DS . 'cache',
            Mage::getBaseDir('var') . DS . 'full_page_cache',
            Mage::getBaseDir('var') . DS . 'locks',
            Mage::getBaseDir('var') . DS . 'log',
            Mage::getBaseDir('var') . DS . 'report'
        );
    }

    /**
     * Get paths that should be ignored when rolling back system snapshots
     *
     * @return array
     */
    public function getRollbackIgnorePaths()
    {
        return array(
            '.svn',
            '.git',
            'maintenance.flag',
            Mage::getBaseDir('var') . DS . 'session',
            Mage::getBaseDir('var') . DS . 'locks',
            Mage::getBaseDir('var') . DS . 'log',
            Mage::getBaseDir('var') . DS . 'report',
            Mage::getBaseDir('app') . DS . 'Mage.php',
            Mage::getBaseDir() . DS . 'errors',
            Mage::getBaseDir() . DS . 'index.php'
        );
    }

    /**
     * Put store into maintenance mode
     *
     * @return bool
     */
    public function turnOnMaintenanceMode()
    {
        $maintenanceFlagFile = $this->getMaintenanceFlagFilePath();
        $result = $this->_filesystem->write($maintenanceFlagFile, 'maintenance', Mage::getBaseDir());

        return $result !== false;
    }

    /**
     * Turn off store maintenance mode
     */
    public function turnOffMaintenanceMode()
    {
        $maintenanceFlagFile = $this->getMaintenanceFlagFilePath();
        $this->_filesystem->delete($maintenanceFlagFile, Mage::getBaseDir());
    }

    /**
     * Get backup create success message by backup type
     *
     * @param string $type
     * @return string
     */
    public function getCreateSuccessMessageByType($type)
    {
        $messagesMap = array(
            self::TYPE_SYSTEM_SNAPSHOT => __('The system backup has been created.'),
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA => __('The system backup (excluding media) has been created.'),
            self::TYPE_MEDIA => __('The database and media backup has been created.'),
            self::TYPE_DB => __('The database backup has been created.')
        );

        if (!isset($messagesMap[$type])) {
            return;
        }

        return $messagesMap[$type];
    }

    /**
     * Get path to maintenance flag file
     *
     * @return string
     */
    protected function getMaintenanceFlagFilePath()
    {
        return Mage::getBaseDir() . DS . 'maintenance.flag';
    }

    /**
     * Invalidate Cache
     *
     * @return Magento_Backup_Helper_Data
     */
    public function invalidateCache()
    {
        /** @var Magento_Core_Model_Cache_Config $config */
        $config = Mage::getObjectManager()->get('Magento_Core_Model_Cache_Config');
        if ($cacheTypes = $config->getTypes()) {
            $cacheTypesList = array_keys($cacheTypes);
            /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
            $cacheTypeList = Mage::getObjectManager()->get('Magento_Core_Model_Cache_TypeListInterface');
            $cacheTypeList->invalidate($cacheTypesList);
        }
        return $this;
    }

    /**
     * Invalidate Indexer
     *
     * @return Magento_Backup_Helper_Data
     */
    public function invalidateIndexer()
    {
        foreach (Mage::getResourceModel('Magento_Index_Model_Resource_Process_Collection') as $process) {
            $process->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
        return $this;
    }

    /**
     * Creates backup's display name from it's name
     *
     * @param string $name
     * @return string
     */
    public function nameToDisplayName($name)
    {
        return str_replace('_', ' ', $name);
    }

    /**
     * Extracts information from backup's filename
     *
     * @param string $filename
     * @return Magento_Object
     */
    public function extractDataFromFilename($filename)
    {
        $extensions = $this->getExtensions();

        $filenameWithoutExtension = $filename;

        foreach ($extensions as $extension) {
            $filenameWithoutExtension = preg_replace('/' . preg_quote($extension, '/') . '$/', '',
                $filenameWithoutExtension
            );
        }

        $filenameWithoutExtension = substr($filenameWithoutExtension, 0, strrpos($filenameWithoutExtension, "."));

        list($time, $type) = explode("_", $filenameWithoutExtension);

        $name = str_replace($time . '_' . $type, '', $filenameWithoutExtension);

        if (!empty($name)) {
            $name = substr($name, 1);
        }

        $result = new Magento_Object();
        $result->addData(array(
            'name' => $name,
            'type' => $type,
            'time' => $time
        ));

        return $result;
    }
}
