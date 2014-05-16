<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Backup\Db;

interface BackupInterface
{
    /**
     * Set backup time
     *
     * @param int $time
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function setTime($time);

    /**
     * Set backup type
     *
     * @param string $type
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function setType($type);

    /**
     * Set backup path
     *
     * @param string $path
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function setPath($path);

    /**
     * Set backup name
     *
     * @param string $name
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function setName($name);

    /**
     * Open backup file (write or read mode)
     *
     * @param bool $write
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function open($write = false);

    /**
     * Write to backup file
     *
     * @param string $data
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function write($data);

    /**
     * Close open backup file
     *
     * @return \Magento\Framework\Backup\Db\BackupInterface
     */
    public function close();
}
