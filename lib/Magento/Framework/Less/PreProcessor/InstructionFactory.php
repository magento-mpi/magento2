<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Less\PreProcessor;

/**
 * Factory class for \Magento\Framework\Less\PreProcessorInterface
 */
class InstructionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Factory constructor
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
     * @param string $className
     * @param array $data
     * @return \Magento\Framework\Less\PreProcessorInterface
     * @throws \InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $preProcessor = $this->objectManager->create($className, $data);
        if (!$preProcessor instanceof \Magento\Framework\Less\PreProcessorInterface) {
            throw new \InvalidArgumentException(
                "{$className} doesn't implement \\Magento\\Framework\\Less\\PreProcessorInterface"
            );
        }
        return $preProcessor;
    }
}
