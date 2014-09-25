<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

/**
 * Factory class for \Magento\Framework\Validator
 */
class ValidatorFactory
{
    const DEFAULT_INSTANCE_NAME = 'Magento\Framework\Validator';

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
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
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = self::DEFAULT_INSTANCE_NAME
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @param string $instanceName
     * @return \Magento\Framework\Validator
     */
    public function create(array $data = [], $instanceName = null)
    {
        if (null === $instanceName) {
            return $this->_objectManager->create($this->_instanceName, $data);
        } else {
            return $this->_objectManager->create($instanceName, $data);
        }

    }
}
