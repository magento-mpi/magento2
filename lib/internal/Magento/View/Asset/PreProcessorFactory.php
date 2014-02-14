<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * View asset pre-processor factory
 */
class PreProcessorFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return PreProcessor\PreProcessorInterface
     * @throws \UnexpectedValueException
     */
    public function create($instanceName, array $data = array())
    {
        $processorInstance = $this->objectManager->create($instanceName, $data);
        if (!($processorInstance instanceof PreProcessor\PreProcessorInterface)) {
            throw new \UnexpectedValueException("$instanceName has to implement the pre-processor interface.");
        }
        return $processorInstance;
    }
}
