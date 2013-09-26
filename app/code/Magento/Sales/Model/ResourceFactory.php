<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for \Magento\Core\Model\Resource\Db\AbstractDb
 */
namespace Magento\Sales\Model;

class ResourceFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function create($instanceName, array $data = array())
    {
        return $this->_objectManager->create($instanceName, $data);
    }
}
