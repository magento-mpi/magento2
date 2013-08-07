<?php
/**
 * {license_notice}
 *
 * @category     Magento
 * @package      Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Filesystem rollback workers abstract class
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Backup_Filesystem_Rollback_Abstract
{
    /**
     * Snapshot object
     *
     * @var Magento_Backup_Filesystem
     */
    protected $_snapshot;

    /**
     * Default worker constructor
     *
     * @param Magento_Backup_Filesystem $snapshotObject
     */
    public function __construct(Magento_Backup_Filesystem $snapshotObject)
    {
        $this->_snapshot = $snapshotObject;
    }

    /**
     * Main worker's function that makes files rollback
     */
    abstract public function run();
}
