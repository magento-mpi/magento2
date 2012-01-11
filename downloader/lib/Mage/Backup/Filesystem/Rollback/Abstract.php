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
 * Filesystem rollback workers abstract class
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Backup_Filesystem_Rollback_Abstract
{
    /**
     * Snapshot object
     *
     * @var Mage_Backup_Filesystem
     */
    protected $_snapshot;

    /**
     * Default worker constructor
     *
     * @param Mage_Backup_Filesystem $snapshotObject
     */
    public function __construct(Mage_Backup_Filesystem $snapshotObject)
    {
        $this->_snapshot = $snapshotObject;
    }

    /**
     * Main worker's function that makes files rollback
     */
    abstract public function run();
}
