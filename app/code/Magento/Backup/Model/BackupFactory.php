<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backup_Model_BackupFactory
{
    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName;

    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento_ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento_ObjectManager $objectManager, $instanceName = 'Magento_Backup_Model_Backup')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return Magento_Backup_Model_Backup
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
