<?php
namespace Magento\View\Layout;

use Magento\ObjectManager;

/**
 * Factory class for \Magento\View\Layout\Processor
 */
class ProcessorFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(ObjectManager $objectManager, $instanceName = 'Magento\View\Layout\Processor')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $arguments
     * @return \Magento\View\Layout\Processor
     */
    public function create(array $arguments = array())
    {
        return $this->objectManager->create($this->instanceName, $arguments);
    }
}
