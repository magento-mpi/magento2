<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model;

class IpnFactory
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
    protected $_instanceName = 'Magento\Paypal\Model\Ipn';

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param array $mapping
     */
    public function __construct(\Magento\ObjectManager $objectManager, array $mapping = array())
    {
        if (isset($mapping['ipn'])) {
            $this->_instanceName = $mapping['ipn'];
        }
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Paypal\Model\IpnInterface
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}