<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Module
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup;

/**
 * Factory class for \Magento\Module\Setup\Migration
 */
class MigrationFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\ObjectManager $objectManager, $instanceName = 'Magento\Module\Setup\Migration')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Module\Setup\Migration
     * @throws \InvalidArgumentException
     */
    public function create(array $data = array())
    {

        $migrationInstance = $this->_objectManager->create($this->_instanceName, $data);

        if (!$migrationInstance instanceof \Magento\Module\Setup\Migration) {
            throw new \InvalidArgumentException(
                $this->_instanceName . ' doesn\'n extend \Magento\Module\Setup\Migration'
            );
        }
        return $migrationInstance;
    }
}
