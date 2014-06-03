<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Backup\Db;

interface BackupDbInterface
{
    /**
     * Create DB backup
     *
     * @param BackupInterface $backup
     * @return void
     */
    public function createBackup(\Magento\Framework\Backup\Db\BackupInterface $backup);
}
