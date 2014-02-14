<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

/**
 * Factory class for Processor
 */
class ProcessorFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\View\Layout\ProcessorInterface'
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