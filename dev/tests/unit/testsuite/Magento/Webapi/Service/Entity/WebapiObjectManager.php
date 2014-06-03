<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class WebapiObjectManager implements \Magento\Framework\ObjectManager
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $factory;

    /**
     * @param \Magento\TestFramework\Helper\ObjectManager $factory
     */
    public function __construct(\Magento\TestFramework\Helper\ObjectManager $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create new object instance
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    public function create($type, array $arguments = array())
    {
        return $this->factory->getObject($type, $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        return $this->factory->getObject($type);
    }

    /**
     * Configure object manager
     *
     * @param array $configuration
     * @return void
     */
    public function configure(array $configuration)
    {
        $this->configuration = $configuration;
    }
}
