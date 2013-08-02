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
 * Class to work with backups
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup
{
    /**
     * List of supported a backup types
     *
     * @var array
     */
    static protected $_allowedBackupTypes = array('db', 'snapshot', 'filesystem', 'media', 'nomedia');

    /**
     * get Backup Instance By File Name
     *
     * @param  string $type
     * @return Mage_Backup_Interface
     */
    static public function getBackupInstance($type)
    {
        $class = 'Mage_Backup_' . ucfirst($type);

        if (!in_array($type, self::$_allowedBackupTypes) || !class_exists($class, true)){
            throw new Magento_Exception('Current implementation not supported this type (' . $type . ') of backup.');
        }

        return new $class();
    }
}
