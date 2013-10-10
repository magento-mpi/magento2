<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Db;

class BackupFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var string
     */
    private $_backupInstanceName;

    /**
     * @var string
     */
    private $_backupDbInstanceName;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param string $backupInstanceName
     * @param string $backupDbInstanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $backupInstanceName,
        $backupDbInstanceName
    ) {
        $this->_objectManager = $objectManager;
        $this->_backupInstanceName = $backupInstanceName;
        $this->_backupDbInstanceName = $backupDbInstanceName;
    }

    /**
     * Create backup model
     *
     * @param array $arguments
     * @return \Magento\Backup\Db\BackupInterface
     */
    public function createBackupModel(array $arguments = array())
    {
        return $this->_objectManager->create($this->_backupInstanceName, $arguments);
    }

    /**
     * Create backup Db model
     *
     * @param array $arguments
     * @return \Magento\Backup\Db\BackupDbInterface
     */
    public function createBackupDbModel(array $arguments = array())
    {
        return $this->_objectManager->create($this->_backupDbInstanceName, $arguments);
    }
}
