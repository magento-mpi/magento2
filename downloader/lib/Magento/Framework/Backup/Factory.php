<?php
/**
 * Backup object factory.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Backup;

class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    /**
     * Backup type constant for database backup
     */
    const TYPE_DB = 'db';

    /**
     * Backup type constant for filesystem backup
     */
    const TYPE_FILESYSTEM = 'filesystem';

    /**
     * Backup type constant for full system backup(database + filesystem)
     */
    const TYPE_SYSTEM_SNAPSHOT = 'snapshot';

    /**
     * Backup type constant for media and database backup
     */
    const TYPE_MEDIA = 'media';

    /**
     * Backup type constant for full system backup excluding media folder
     */
    const TYPE_SNAPSHOT_WITHOUT_MEDIA = 'nomedia';

    /**
     * List of supported a backup types
     *
     * @var array
     */
    protected $_allowedTypes;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_allowedTypes = array(
            self::TYPE_DB,
            self::TYPE_FILESYSTEM,
            self::TYPE_SYSTEM_SNAPSHOT,
            self::TYPE_MEDIA,
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA
        );
    }

    /**
     * Create new backup instance
     *
     * @param string $type
     * @return \Magento\Framework\Backup\BackupInterface
     * @throws \Magento\Framework\Exception
     */
    public function create($type)
    {
        if (!in_array($type, $this->_allowedTypes)) {
            throw new \Magento\Framework\Exception('Current implementation not supported this type (' . $type . ') of backup.');
        }
        $class = 'Magento\Framework\Backup\\' . ucfirst($type);
        return $this->_objectManager->create($class);
    }
}
