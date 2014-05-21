<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

/**
 * View asset pre-processor factory
 */
class PreProcessorFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Object manager
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento\Framework\View\Asset\PreProcessorInterface
     * @throws \UnexpectedValueException
     */
    public function create($instanceName, array $data = array())
    {
        $processorInstance = $this->objectManager->create($instanceName, $data);
        if (!$processorInstance instanceof \Magento\Framework\View\Asset\PreProcessorInterface) {
            throw new \UnexpectedValueException("{$instanceName} has to implement the pre-processor interface.");
        }
        return $processorInstance;
    }
}
