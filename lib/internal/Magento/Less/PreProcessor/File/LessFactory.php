<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\File;

/**
 * Factory class for \Magento\Less\PreProcessor\File\Less
 */
class LessFactory
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
        $instanceName = 'Magento\Less\PreProcessor\File\Less'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Less\PreProcessor\File\Less
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
