<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backup_Model_DbFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento_Backup_Model_Db';

    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @return Magento_Backup_Model_Db
     */
    public function create()
    {
        return $this->_objectManager->create(self::CLASS_NAME);
    }
}