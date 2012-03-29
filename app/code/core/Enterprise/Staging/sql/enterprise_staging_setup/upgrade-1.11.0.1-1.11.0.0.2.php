<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fix for incorrectly created FK for backup tables
 */

/** @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

/**
 * Collect all existing backup tables
 */
$backupTablesList = array();

$stagingCollection = Mage::getResourceModel('Enterprise_Staging_Model_Resource_Staging_Collection');
while ($staging = $stagingCollection->fetchItem()) {
    $logCollection = Mage::getResourceModel('Enterprise_Staging_Model_Resource_Staging_Log_Collection')
        ->addFieldToFilter('action', Enterprise_Staging_Model_Staging_Config::ACTION_BACKUP)
        ->addFieldToFilter('status', Enterprise_Staging_Model_Staging_Config::STATUS_STARTED)
        ->addFieldToFilter('staging_id', $staging->getId());

    while ($log = $logCollection->fetchItem()) {
        $staging->getMapperInstance()->unserialize($log->getMap());
        $staging->collectBackupTables($log);

        $backupTables = $staging->getBackupTables();
        if (!is_array($backupTables) || empty($backupTables)) {
            continue;
        }

        $tablePrefix = Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getTablePrefix($staging)
            . Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getStagingBackupTablePrefix()
            . $log->getId() . '_';

        foreach ($backupTables as $backupTable) {
            $tableName = $tablePrefix . $backupTable;
            if ($installer->tableExists($tableName)) {
                $backupTablesList[] = $tableName;
            }
        }

        $staging->unsetData('backup_tables');
    }
}

/**
 * Drop backup tables FK and recreate them using correct rules
 */
if (!empty($backupTablesList)) {
    $connection = $installer->getConnection();

    foreach ($backupTablesList as $backupTable) {
        foreach($connection->getForeignKeys($backupTable) as $keyName => $keyInfo) {
            $connection->dropForeignKey($backupTable, $keyName);

            $correctFkName = $connection->getForeignKeyName(
                $keyInfo['TABLE_NAME'],
                $keyInfo['COLUMN_NAME'],
                $keyInfo['REF_TABLE_NAME'],
                $keyInfo['REF_COLUMN_NAME']
            );

            $connection->addForeignKey(
                $correctFkName,
                $keyInfo['TABLE_NAME'],
                $keyInfo['COLUMN_NAME'],
                $keyInfo['REF_TABLE_NAME'],
                $keyInfo['REF_COLUMN_NAME']
            );
        }
    }
}
