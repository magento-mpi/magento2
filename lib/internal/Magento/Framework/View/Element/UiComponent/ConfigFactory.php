<?php
namespace Magento\Framework\View\Element\UiComponent;

/**
 * Factory class for \Magento\Framework\View\Element\UiComponent\ConfigInterface
 */
class ConfigFactory
{
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
        $instanceName = 'Magento\Framework\View\Element\UiComponent\ConfigInterface'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Ui\Configuration
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
