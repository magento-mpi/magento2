<?php
namespace Magento\View;

use Magento\ObjectManager;

/**
 * Factory class for \Magento\View\Layout
 */
class LayoutFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
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
    public function __construct(ObjectManager $objectManager, $instanceName = 'Magento\View\Layout')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $arguments
     * @return \Magento\View\Layout
     */
    public function create(array $arguments = array())
    {
        return $this->objectManager->create($this->instanceName, $arguments);
    }
}
