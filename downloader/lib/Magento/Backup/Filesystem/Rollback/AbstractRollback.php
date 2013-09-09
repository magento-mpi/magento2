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
 * Filesystem rollback workers abstract class
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Filesystem\Rollback;

abstract class AbstractRollback
{
    /**
     * Snapshot object
     *
     * @var \Magento\Backup\Filesystem
     */
    protected $_snapshot;

    /**
     * Default worker constructor
     *
     * @param \Magento\Backup\Filesystem $snapshotObject
     */
    public function __construct(\Magento\Backup\Filesystem $snapshotObject)
    {
        $this->_snapshot = $snapshotObject;
    }

    /**
     * Main worker's function that makes files rollback
     */
    abstract public function run();
}
