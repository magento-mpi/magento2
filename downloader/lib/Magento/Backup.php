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
 * Class to work with backups
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento;

class Backup
{
    /**
     * List of supported a backup types
     *
     * @var array
     */
    protected static $_allowedBackupTypes = array('db', 'snapshot', 'filesystem', 'media', 'nomedia');

    /**
     * get Backup Instance By File Name
     *
     * @param  string $type
     * @throws \Magento\Exception
     * @return \Magento\Framework\Backup\BackupInterface
     */
    public static function getBackupInstance($type)
    {
        $class = 'Magento\Framework\Backup\\' . ucfirst($type);

        if (!in_array($type, self::$_allowedBackupTypes) || !class_exists($class, true)) {
            throw new \Magento\Exception('Current implementation not supported this type (' . $type . ') of backup.');
        }

        return new $class();
    }
}
