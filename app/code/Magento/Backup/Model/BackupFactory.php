<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup model factory
 *
 * @method \Magento\Backup\Model\Backup create($timestamp, $type)
 */
namespace Magento\Backup\Model;

class BackupFactory
{
    /**
     * @var \Magento\Backup\Model\Fs\Collection
     */
    protected $_fsCollection;

    /**
     * @var \Magento\Backup\Model\Fs\Collection
     */
    protected $_backupInstance;

    /**
     * @param \Magento\Backup\Model\Fs\Collection $fsCollection
     * @param \Magento\Backup\Model\Backup $backup
     */
    public function __construct(
        \Magento\Backup\Model\Fs\Collection $fsCollection,
        \Magento\Backup\Model\Backup $backup
    ) {
        $this->_fsCollection = $fsCollection;
        $this->_backupInstance = $backup;
    }

    /**
     * Load backup by it's type and creation timestamp
     *
     * @param int $timestamp
     * @param string $type
     * @return \Magento\Backup\Model\Backup
     */
    public function create($timestamp, $type)
    {
        $backupId = $timestamp . '_' . $type;

        foreach ($this->_fsCollection as $backup) {
            if ($backup->getId() == $backupId) {
                $this->_backupInstance->setType($backup->getType())
                    ->setTime($backup->getTime())
                    ->setName($backup->getName())
                    ->setPath($backup->getPath());
                break;
            }
        }
        return $this->_backupInstance;
    }
}
