<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FactoryStub implements \Magento\Framework\ObjectManager\FactoryInterface
{
    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $config
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\ObjectManager\DefinitionInterface $definitions
     * @param array $globalArguments
     * @throws \BadMethodCallException
     */
    public function __construct($config, $objectManager = null, $definitions = null, $globalArguments = array())
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws \BadMethodCallException
     */
    public function create($requestedType, array $arguments = array())
    {
        throw new \BadMethodCallException(__METHOD__);
    }
}
