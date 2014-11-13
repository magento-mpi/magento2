<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\ObjectManager\FactoryInterface;

/**
 * A wrapper around object manager with workarounds to access it in client code
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObjectManager extends \Magento\Framework\ObjectManager\ObjectManager
{
    /**
     * @var ObjectManager
     */
    protected static $_instance;

    /**
     * Retrieve object manager
     *
     * TODO: Temporary solution for serialization, should be removed when Serialization problem is resolved
     *
     * @deprecated
     * @return ObjectManager
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof \Magento\Framework\ObjectManagerInterface) {
            throw new \RuntimeException('ObjectManager isn\'t initialized');
        }
        return self::$_instance;
    }

    /**
     * Set object manager instance
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @throws \LogicException
     * @return void
     */
    public static function setInstance(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        self::$_instance = $objectManager;
    }

    /**
     * @param FactoryInterface $factory
     * @param \Magento\Framework\ObjectManager\ConfigInterface $config
     * @param array $sharedInstances
     */
    public function __construct(
        FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config,
        array $sharedInstances = array()
    ) {
        parent::__construct($factory, $config, $sharedInstances);
        self::$_instance = $this;
    }
}
