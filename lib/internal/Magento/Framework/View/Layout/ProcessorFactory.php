<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

/**
 * Factory class for Processor
 */
class ProcessorFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\View\Layout\ProcessorInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return ProcessorInterface
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
