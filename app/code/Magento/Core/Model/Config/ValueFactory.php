<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Config;

/**
 * Factory class
 */
class ValueFactory
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
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\App\Config\ValueInterface'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\App\Config\ValueInterface
     * @throws \InvalidArgumentException
     */
    public function create(array $data = array())
    {
        $model = $this->_objectManager->create($this->_instanceName, $data);
        if (!$model instanceof \Magento\App\Config\ValueInterface) {
            throw new \InvalidArgumentException('Invalid config field model: ' . $this->_instanceName);
        }
        return $model;
    }
}
