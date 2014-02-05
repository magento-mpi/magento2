<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;
use Magento\ObjectManager\Factory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObjectManager extends \Magento\ObjectManager\ObjectManager
{
    /**
     * @var \Magento\App\ObjectManager
     */
    protected static $_instance;

    /**
     * @var \Magento\ObjectManager\Relations
     */
    protected $_compiledRelations;

    /**
     * Retrieve object manager
     *
     * TODO: Temporary solution for serialization, should be removed when Serialization problem is resolved
     *
     * @deprecated
     * @return \Magento\App\ObjectManager
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof \Magento\ObjectManager) {
            throw new \RuntimeException('ObjectManager isn\'t initialized');
        }
        return self::$_instance;
    }

    /**
     * Set object manager instance
     *
     * @param \Magento\ObjectManager $objectManager
     * @throws \LogicException
     * @return void
     */
    public static function setInstance(\Magento\ObjectManager $objectManager)
    {
        self::$_instance = $objectManager;
    }

    /**
     * @param Factory $factory
     * @param \Magento\ObjectManager\Config $config
     * @param array $sharedInstances
     */
    public function __construct(
        Factory $factory, \Magento\ObjectManager\Config $config, array $sharedInstances = array()
    ) {
        parent::__construct($factory, $config, $sharedInstances);
        self::$_instance = $this;
    }
}
