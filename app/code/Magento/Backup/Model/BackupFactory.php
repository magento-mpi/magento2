<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
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
        $fsCollection = $this->_objectManager->get('Magento\Backup\Model\Fs\Collection');
        $backupInstance = $this->_objectManager->get('Magento\Backup\Model\Backup');
        foreach ($fsCollection as $backup) {
            if ($backup->getId() == $backupId) {
                $backupInstance->setType(
                    $backup->getType()
                )->setTime(
                    $backup->getTime()
                )->setName(
                    $backup->getName()
                )->setPath(
                    $backup->getPath()
                );
                break;
            }
        }
        return $backupInstance;
    }
}
