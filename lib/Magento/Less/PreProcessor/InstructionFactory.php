<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor;

/**
 * Factory class for \Magento\Less\PreProcessorInterface
 */
class InstructionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\Less\PreProcessorInterface
     */
    public function create($className, array $data = [])
    {
        $preProcessor = $this->_objectManager->create($className, $data);
        if (!$preProcessor instanceof \Magento\Less\PreProcessorInterface) {
            throw new \InvalidArgumentException(
                "{$className} aren't instance of \\Magento\\Less\\PreProcessorInterface"
            );
        }
        return $preProcessor;
    }
}
