<?php
/**
 * Backup object factory.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento;

class BackupFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * List of supported a backup types
     *
     * @var array
     */
    private $_allowedTypes = array('db', 'snapshot', 'filesystem', 'media', 'nomedia');

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new backup instance
     *
     * @param string $type
     * @return \Magento\Backup\BackupInterface
     * @throws \Magento\Exception
     */
    public function create($type)
    {
        if (!in_array($type, $this->_allowedTypes)) {
            throw new \Magento\Exception('Current implementation not supported this type (' . $type . ') of backup.');
        }
        $class = 'Magento\Backup\\' . ucfirst($type);
        return $this->_objectManager->create($class);
    }
}