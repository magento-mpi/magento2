<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Provider;

use Magento\Framework\ObjectManager;

/**
 * Class ProviderFactory
 */
class ProviderFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Getting provider object
     *
     * @param string $class
     * @param array $arguments
     * @return ProviderInterface
     * @throws \InvalidArgumentException
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        if (!($object instanceof ProviderInterface)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" must implement the interface \Magento\Ui\Provider\ProviderInterface', $class)
            );
        }

        return $object;
    }
}
